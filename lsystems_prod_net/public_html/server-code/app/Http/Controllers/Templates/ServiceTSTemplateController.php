<?php

namespace App\Http\Controllers;

class ServiceTSTemplateController extends Controller
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

    static function build($args, $moduleName, $isMobile) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $content = "import { Injectable } from '@angular/core';\n";
        if ($isMobile == false) {
            $content .= "import { HttpClient } from '@angular/common/http';\n";
            $content .= "import { HttpHeaders } from '@angular/common/http';\n";
        } else {
            $content .= "import { HTTP } from '@ionic-native/http/ngx';\n";
        }
        $content .= "import { Router } from '@angular/router';\n";
        $content .= "import { environment } from './../../../../environments/environment';\n";
        $content .= "import { ".$tableNameSingular." } from './../../../models/".strtoupper($moduleName)."/".$tableNameSingular."';\n\n";
        $content .= "@Injectable({\n";
        $content .= "   providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."Service {\n\n";
        $content .= "   url = environment.api_".strtolower($moduleName)." + '".strtolower($tableNameSingular)."/';\n";
        if ($isMobile == false) {
            $content .= "   options = {headers: null};\n\n";
            $content .= "   constructor(private http: HttpClient, private router: Router) {\n";
            $content .= "      this.options.headers = new HttpHeaders({'api_token': sessionStorage.getItem('api_token')});\n";
        } else {
            $content .= "   options = null;\n\n";
            $content .= "   constructor(private http: HTTP, private router: Router) {\n";
            $content .= "      this.http.setDataSerializer('json');\n";
            $content .= "      this.options = {'api_token': sessionStorage.getItem('api_token'), 'Content-Type': 'application/json'};\n";
        }
        $content .= "   }\n\n";
        $content .= "   get(id?: number): Promise<any> {\n";
        $content .= "      if (typeof id === 'undefined') {\n";
        if ($isMobile == false) {
            $content .= "         return this.http.get(this.url, this.options).toPromise()\n";
            $content .= "         .then( r => {\n";
            $content .= "            return r;\n";
        } else {
            $content .= "         return this.http.get(this.url, {}, this.options)\n";
            $content .= "         .then( r => {\n";
            $content .= "            return JSON.parse(r.data);\n";
        }
        $content .= "         }).catch( error => { this.handledError(error);  });\n";
        $content .= "      }\n";
        if ($isMobile == false) {
            $content .= "      return this.http.get(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.get(this.url + '?id=' + id.toString(), {}, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n\n";
        $content .= "   get_paginate(size: number, page: number): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), {}, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error);  });\n";
        $content .= "   }\n\n";
        $content .= "   delete(id: number): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.delete(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.delete(this.url + '?id=' + id.toString(), {}, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n\n";
        $content .= "   getBackUp(): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.get(this.url + 'backup', this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.get(this.url + 'backup', {}, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n\n";
        $content .= "   post(".strtolower($tableNameSingular).": ".$tableNameSingular."): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.post(this.url, JSON.stringify(".strtolower($tableNameSingular)."), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.post(this.url, ".strtolower($tableNameSingular).", this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n\n";
        $content .= "   put(".strtolower($tableNameSingular).": ".$tableNameSingular."): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.put(this.url, JSON.stringify(".strtolower($tableNameSingular)."), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.put(this.url, ".strtolower($tableNameSingular).", this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n\n";
        $content .= "   masiveLoad(data: any[]): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.post(this.url + 'masive_load', JSON.stringify({data: data}), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.post(this.url + 'masive_load', {data: data}, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n\n";
        $content .= "   handledError(error: any) {\n";
        $content .= "      console.log(error);\n";
        $content .= "      sessionStorage.clear();\n";
        $content .= "      this.router.navigate(['/login']);\n";
        $content .= "   }\n";
        $content .= "}";
        return ["Table"=>$tableNameSingular, "Content"=>$content];
    }
}
