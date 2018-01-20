<?php

namespace App\Controller;

use App\Service\StorageLocator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
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
}
