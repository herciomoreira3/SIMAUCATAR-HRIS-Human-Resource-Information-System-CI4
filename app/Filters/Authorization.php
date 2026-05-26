<?php

namespace App\Filters;

use App\Models\ApplicationModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Authorization implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */

    protected $ApplicationModel;
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri                     = service('uri');
        $this->ApplicationModel  = new ApplicationModel();
        
        // Use the full relative path instead of just the first segment
        // This allows routes like 'administrador/dashboard' to match user_menu entries
        $path = $uri->getPath();
        
        if ($path) {
            $menu = $this->ApplicationModel->getMenuByUrl($path);
            
            // If no exact match, try the first segment (fallback for existing logic)
            if (!$menu) {
                $segment = $uri->getSegment(1);
                $menu = $this->ApplicationModel->getMenuByUrl($segment);
            }

            if (!$menu) {
                // If still not found, it might be a public page or not a menu-controlled page
                // We'll let it pass for now or redirect if it's meant to be protected.
                // In this template, it seems unknown menus are redirected to /
                return; 
            } else {
                $dataAccess = [
                    'roleID' => session()->get('role'),
                    'menuID' => $menu['id']
                ];
                $userAccess = $this->ApplicationModel->checkUserAccess($dataAccess);
                if (!$userAccess) {
                    // not granted
                    return redirect()->to(base_url('blocked'));
                }
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
