<?php

namespace App\Http\Controllers;

class UserServiceTemplateController extends Controller
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

    static function build($args, $isMobile) {
        $moduleName = $args['moduleName'];
        $content = "import { Injectable } from '@angular/core';\n";
        if ($isMobile == false) {
            $content .= "import { HttpClient } from '@angular/common/http';\n";
            $content .= "import { HttpHeaders } from '@angular/common/http';\n";
        } else {
            $content .= "import { HTTP } from '@ionic-native/http/ngx';\n";
        }
        $content .= "import { environment } from './../../../environments/environment';\n";
        $content .= "import { User } from './../../models/profile/User';\n";
        $content .= "\n";
        $content .= "@Injectable({\n";
        $content .= "   providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class UserService {\n";
        $content .= "\n";
        $content .= "   url = environment.api_".strtolower($moduleName)." + 'user/';\n";
        if ($isMobile == false) {
            $content .= "   options = {headers: null};\n\n";
            $content .= "   constructor(private http: HttpClient) {\n";
            $content .= "      this.options.headers = new HttpHeaders({'api_token': sessionStorage.getItem('api_token')});\n";
        } else {
            $content .= "   options = null;\n\n";
            $content .= "   constructor(private http: HTTP) {\n";
            $content .= "      this.http.setDataSerializer('json');\n";
            $content .= "      this.options = {'api_token': sessionStorage.getItem('api_token'), 'Content-Type': 'application/json'};\n";
        }
        $content .= "   }\n";
        $content .= "\n";
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
        $content .= "         }).catch( error => { this.handledError(error); });\n";
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
        $content .= "   }\n";
        $content .= "   \n";
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
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n";
        $content .= "   \n";
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
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   post(user: User): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.post(this.url, JSON.stringify(user), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.post(this.url, user, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   put(user: User): Promise<any> {\n";
        if ($isMobile == false) {
            $content .= "      return this.http.put(this.url, JSON.stringify(user), this.options).toPromise()\n";
            $content .= "      .then( r => {\n";
            $content .= "         return r;\n";
        } else {
            $content .= "      return this.http.put(this.url, user, this.options)\n";
            $content .= "      .then( r => {\n";
            $content .= "         return JSON.parse(r.data);\n";
        }
        $content .= "      }).catch( error => { this.handledError(error); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   handledError(error: any) {\n";
        $content .= "      console.log(error);\n";
        $content .= "   }\n";
        $content .= "}\n";
        return ["Content"=>$content];
    }
}
