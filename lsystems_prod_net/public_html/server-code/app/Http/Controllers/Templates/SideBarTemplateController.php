<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class SideBarTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    static function build($args) {
        $models = $args['models'];
        $type = $args['type'];
        $moduleName = $args['moduleName'];
        if($type === 'bootstrap') {
            $content = "<nav class=\"sidebar\" [ngClass]=\"{ sidebarPushRight: isActive, collapsed: collapsed }\">\n";
            $content .= "   <div class=\"list-group\">\n";
            $content .= "      <a [routerLink]=\"['/main']\" [routerLinkActive]=\"['router-link-active']\" class=\"list-group-item\">\n";
            $content .= "         <i class=\"fas fa-tachometer-alt\"></i>&nbsp; <span>Main</span>\n";
            $content .= "      </a>\n";
            $content .= "      <div class=\"nested-menu\">\n";
            $content .= "         <a class=\"list-group-item\" (click)=\"addExpandClass('bdd ".strtolower($moduleName)."')\">\n";
            $content .= "            <span class=\"fas fa-database\"></span>&nbsp;BDD ".strtoupper($moduleName)."\n";
            $content .= "         </a>\n";
            $content .= "         <li class=\"nested\" [class.expand]=\"showMenu === 'bdd ".strtolower($moduleName)."'\">\n";
            $content .= "            <ul class=\"submenu\">\n";
            $content .= "\n\n               <!--".$moduleName."-->\n\n";
            foreach($models as $modelo) {
                $model = $modelo['Table']['nameSingular'];
                $content .= "               <li>\n";
                $content .= "                  <a [routerLink]=\"['/".UtilitiesController::checkNames($model)."']\" [routerLinkActive]=\"['router-link-active']\" class=\"list-group-item\">".$model." </a>\n";
                $content .= "               </li>\n";
            }
            $content .= "            </ul>\n";
            $content .= "         </li>\n";
            $content .= "      </div>\n";
            $content .= "      <div class=\"nested-menu\">\n";
            $content .= "         <a class=\"list-group-item\" (click)=\"addExpandClass('profile')\">\n";
            $content .= "            <span><img class=\"rounded-circle\" src=\"{{profileImg}}\" width=\"32px\" height=\"32px\"></span>&nbsp;<small>{{ user.name }}</small>\n";
            $content .= "         </a>\n";
            $content .= "         <li class=\"nested\" [class.expand]=\"showMenu === 'profile'\">\n";
            $content .= "            <ul class=\"submenu\">\n";
            $content .= "               <li>\n";
            $content .= "                  <a [routerLink]=\"['/profile']\" [routerLinkActive]=\"['router-link-active']\" class=\"list-group-item\">Perfil </a>\n";
            $content .= "               </li>\n";
            $content .= "               <li>\n";
            $content .= "                  <a [routerLink]=\"['/login']\" (click)=\"logOut()\"><span>&nbsp;Cerrar Sesión</span></a>\n";
            $content .= "               </li>\n";
            $content .= "            </ul>\n";
            $content .= "         </li>\n";
            $content .= "      </div>\n";
            $content .= "   </div>\n";
            $content .= "</nav>\n";
        }
        if($type === 'metro') {
            $content = "<div class=\"sidebar-header bg-darkGrayBlue fg-white\" data-image=\"images/sb-bg-1.jpg\">\n";
            $content .= "   <a class=\"fg-white sub-action\"[routerLink]=\"['/login']\" (click)=\"logOut()\">\n";
            $content .= "      <span class=\"mif-settings-power mif-2x\"></span>\n";
            $content .= "   </a>\n";
            $content .= "   <a class=\"avatar\" [routerLink]=\"['/profile']\" [routerLinkActive]=\"['router-link-active']\">\n";
            $content .= "      <img src=\"{{profileImg}}\"/>\n";
            $content .= "   </a>\n";
            $content .= "   <span class=\"title\">{{ user.name }}</span>\n";
            $content .= "   <span class=\"subtitle\"> 2019 © LSystems</span>\n";
            $content .= "</div>\n";
            $content .= "<ul class=\"sidebar-menu\">\n";
            $content .= "   <li class=\"group-title\">BDD</li>\n";
            foreach($models as $modelo) {
                $model = $modelo['Table']['nameSingular'];
                $content .= "   <li>\n";
                $content .= "      <a [routerLink]=\"['/".UtilitiesController::checkNames($model)."']\" [routerLinkActive]=\"['router-link-active']\">".$model." </a>\n";
                $content .= "   </li>\n";
            }
            $content .= "</ul>\n";
        }
        return ["Content"=>$content];
    }
}
