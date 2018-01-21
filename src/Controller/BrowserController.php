<?php

namespace App\Controller;

use App\Service\StorageLocator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Index directories and serve files of the storage directory.
 *
 * This controller is responsible for indexing the storage directory and serving
 * files of it to the user.
 *
 * NOTE: As this controller defines catch-all routes, it should be registered
 *       after all other controllers have been registered, so others get matched
 *       first.
 *
 *
 * @Route(methods={"GET"})
 */
class BrowserController extends Controller
{
    /**
     * Index a directory.
     *
     * This route matches all directories (i.e. routes ending with a slash) and
     * will return the directory listing for this directory.
     *
     *
     * @param StorageLocator $locator the storage locator to be used
     * @param string         $path    the directory to be indexed
     *
     * @throws NotFoundHttpException the directory doesn't exist
     *
     * @return Response the directory listing of `$path`
     *
     *
     * @Route(
     *     "/{path}",
     *     defaults={"path": null},
     *     requirements={"path": ".+[/]"}
     * )
     */
    public function index(
        StorageLocator $locator,
        string $path = null
    ): Response {
        /* Get the absolute path of the directory inside the storage. If the
         * directory doesn't exist, a NotFoundHttpException will be thrown,
         * resulting in an error 404. */
        $dir = $locator->getAbsolutePath($path);
        if (!file_exists($dir) || !is_dir($dir)) {
            throw new NotFoundHttpException(sprintf(
                "Directory '%s' doesn't exist in the storage.",
                $path
            ));
        }

        /* Find all files and directories in '$path', but don't recurse into
         * them, as this will be done in further requests. Directories will be
         * listed before files, both ordered alphabetically. */
        $finder = new Finder();
        $finder->in($dir)
               ->depth(0)
               ->ignoreDotFiles(true)
               ->sortByType();

        /* Return the rendered view for the index of this directory. The
         * template has full access to the files and directories found, so any
         * further handling of these may be done inside the template. */
        return $this->render('browser/index.html.twig', [
            'files' => $finder,
        ]);
    }

    /**
     * Serve a file.
     *
     * This route matches all files (i.e. routes *NOT* ending with a slash) and
     * will return the file with additional headers set.
     *
     *
     * @param StorageLocator $locator the storage locator to be used
     * @param string         $path    the file to be served
     *
     * @throws NotFoundHttpException the file doesn't exist
     *
     * @return BinaryFileResponse response containing the file
     * @return RedirectResponse   redirect to the directory's index, if `$path`
     *                            is a directory
     *
     * @Route(
     *     "/{path}",
     *     requirements={"path": ".+"}
     * )
     */
    public function serveFile(StorageLocator $locator, string $path): Response
    {
        /* Get the absolute path of the file. If the file doesn't exist, a
         * NotFoundHttpException will be thrown, resulting in an error 404. */
        $file = $locator->getAbsolutePath($path);
        if (!file_exists($file)) {
            throw new NotFoundHttpException(sprintf(
                "File '%s' doesn't exist in the storage.",
                $path
            ));
        }

        /* If the file is a directory, redirect the user to the directory
         * listing for this directory instead of throwing an error 404. */
        if (is_dir($file)) {
            return $this->redirect('/'.$path.'/');
        }

        /* Return a response to serve the required file. The disposition will be
         + inline, so the user is able to watch the file inside the browser, if
         * the browser supports the filetype. */
        return $this->file($file, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
