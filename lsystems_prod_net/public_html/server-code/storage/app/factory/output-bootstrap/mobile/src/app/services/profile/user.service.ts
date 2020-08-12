import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { environment } from './../../../environments/environment';
import { User } from './../../models/profile/User';

@Injectable({
   providedIn: 'root'
})
export class UserService {

   url = environment.api_videoconference + 'user/';
   options = null;

   constructor(private http: HTTP) {
      this.options = {'api_token': sessionStorage.getItem('api_token'), 'Content-Type': 'application/json'};
   }

   get(id?: number): Promise<any> {
      if (typeof id === 'undefined') {
         return this.http.get(this.url, {}, this.options)
         .then( r => {
            return JSON.parse(r.data);
         }).catch( error => { this.handledError(error); });
      }
      return this.http.get(this.url + '?id=' + id.toString(), {}, this.options)
      .then( r => {
         return JSON.parse(r.data);
      }).catch( error => { this.handledError(error); });
   }
   
   get_paginate(size: number, page: number): Promise<any> {
      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), {}, this.options)
      .then( r => {
         return JSON.parse(r.data);
      }).catch( error => { this.handledError(error); });
   }
   
   delete(id: number): Promise<any> {
      return this.http.delete(this.url + '?id=' + id.toString(), {}, this.options)
      .then( r => {
         return JSON.parse(r.data);
      }).catch( error => { this.handledError(error); });
   }
   
   post(user: User): Promise<any> {
      return this.http.post(this.url, user, this.options)
      .then( r => {
         return JSON.parse(r.data);
      }).catch( error => { this.handledError(error); });
   }
   
   put(user: User): Promise<any> {
      return this.http.put(this.url, user, this.options)
      .then( r => {
         return JSON.parse(r.data);
      }).catch( error => { this.handledError(error); });
   }
   
   handledError(error: any) {
      console.log(error);
   }
}
