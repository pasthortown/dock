<?php

namespace App\Http\Controllers;

class AuthTemplateController extends Controller
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
        $content .= "import { environment } from './../../environments/environment';\n";
        $content .= "\n";
        $content .= "@Injectable({\n";
        $content .= "  providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class AuthService {\n";
        $content .= "\n";
        if ($isMobile == false) {
            $content .= "  constructor(private http: HttpClient) {}\n";
        } else {
            $content .= "   options = null;\n\n";
            $content .= "   constructor(private http: HTTP) {\n";
            $content .= "      this.http.setDataSerializer('json');\n";
            $content .= "      this.options = {'Content-Type': 'application/json'};\n";
            $content .= "   }\n";
        }
        $content .= "  \n";
        $content .= "  login(email: String, password: String): Promise<any> {\n";
        $content .= "    const data = {email: email, password: password};\n";
        if ($isMobile == false) {
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'login', JSON.stringify(data)).toPromise()\n";
            $content .= "    .then( r =>\n";
            $content .= "      r\n";
        } else {
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'login', data, this.options)\n";
            $content .= "    .then( r =>\n";
            $content .= "      JSON.parse(r.data)\n";
        }
        $content .= "    ).catch( error => { console.log(error); });\n";
        $content .= "  }\n";
        $content .= "  \n";
        $content .= "  register(name: String, email: String): Promise<any> {\n";
        $content .= "    const data = {name: name, email: email};\n";
        if ($isMobile == false) {
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'register', JSON.stringify(data)).toPromise()\n";
            $content .= "    .then( r =>\n";
            $content .= "      r\n";
        } else {
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'register', data, this.options)\n";
            $content .= "    .then( r =>\n";
            $content .= "      JSON.parse(r.data)\n";
        }
        $content .= "    ).catch( error => { console.log(error); });\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  password_recovery_request(email: String): Promise<any> {\n";
        $content .= "    const data = {email: email};\n";
        if ($isMobile == false) {
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'password_recovery_request', JSON.stringify(data)).toPromise()\n";
            $content .= "    .then( r =>\n";
            $content .= "      r\n";
        } else {
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'password_recovery_request', data, this.options)\n";
            $content .= "    .then( r =>\n";
            $content .= "      JSON.parse(r.data)\n";
        }
        $content .= "    ).catch( error => { console.log(error); });\n";
        $content .= "  }\n";
        $content .= "  \n";
        $content .= "  password_change(new_password: String): Promise<any> {\n";
        $content .= "    const data = {new_password: new_password};\n";
        if ($isMobile == false) {
            $content .= "    const options = {headers: null};\n";
            $content .= "    options.headers = new HttpHeaders({'api_token': sessionStorage.getItem('api_token')});\n";
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'user/password_change', JSON.stringify(data), options).toPromise()\n";
            $content .= "    .then( r =>\n";
            $content .= "      r\n";
        } else {
            $content .= "    this.options = {'api_token': sessionStorage.getItem('api_token'), 'Content-Type': 'application/json'};\n";
            $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'user/password_change', data, this.options)\n";
            $content .= "    .then( r =>\n";
            $content .= "      JSON.parse(r.data)\n";
        }
        $content .= "    ).catch( error => { console.log(error); });\n";
        $content .= "  }\n";
        $content .= "}\n";
        return ["Content"=>$content];
    }
}
