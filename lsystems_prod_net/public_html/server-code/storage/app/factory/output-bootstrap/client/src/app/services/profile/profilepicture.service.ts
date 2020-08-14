import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';
import { environment } from './../../../environments/environment';
import { ProfilePicture } from './../../models/profile/ProfilePicture';

@Injectable({
providedIn: 'root'
})
export class ProfilePictureService {

   url = environment.api_lstracking + 'profilepicture/';
   options = {headers: null};

   constructor(private http: HttpClient) {
      this.options.headers = new HttpHeaders({'api_token': sessionStorage.getItem('api_token')});
   }
   
   get(user_id: number): Promise<any> {
      return this.http.get(this.url + '?user_id=' + user_id.toString(), this.options).toPromise()
      .then( r => {
         return r;
      }).catch( error => { this.handledError(error); });
   }
   
   get_paginate(size: number, page: number): Promise<any> {
      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), this.options).toPromise()
      .then( r => {
         return r;
      }).catch( error => { this.handledError(error); });
   }
   
   delete(id: number): Promise<any> {
      return this.http.delete(this.url + '?id=' + id.toString(), this.options).toPromise()
      .then( r => {
         return r;
      }).catch( error => { this.handledError(error); });
   }
   
   post(profilepicture: ProfilePicture): Promise<any> {
      return this.http.post(this.url, JSON.stringify(profilepicture), this.options).toPromise()
      .then( r => {
         return r;
      }).catch( error => { this.handledError(error); });
   }
   
   put(profilepicture: ProfilePicture): Promise<any> {
      return this.http.put(this.url, JSON.stringify(profilepicture), this.options).toPromise()
      .then( r => {
         return r;
      }).catch( error => { this.handledError(error); });
   }
   
   handledError(error: any) {
      console.log(error);
   }
}
