import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { environment } from './../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

   options = null;

   constructor(private http: HTTP) {
      this.http.setDataSerializer('json');
      this.options = {'Content-Type': 'application/json'};
   }
  
  login(email: String, password: String): Promise<any> {
    const data = {email: email, password: password};
    return this.http.post(environment.api_videoconference + 'login', data, this.options)
    .then( r =>
      JSON.parse(r.data)
    ).catch( error => { console.log(error); });
  }
  
  register(name: String, email: String): Promise<any> {
    const data = {name: name, email: email};
    return this.http.post(environment.api_videoconference + 'register', data, this.options)
    .then( r =>
      JSON.parse(r.data)
    ).catch( error => { console.log(error); });
  }

  password_recovery_request(email: String): Promise<any> {
    const data = {email: email};
    return this.http.post(environment.api_videoconference + 'password_recovery_request', data, this.options)
    .then( r =>
      JSON.parse(r.data)
    ).catch( error => { console.log(error); });
  }
  
  password_change(new_password: String): Promise<any> {
    const data = {new_password: new_password};
    this.options = {'api_token': sessionStorage.getItem('api_token'), 'Content-Type': 'application/json'};
    return this.http.post(environment.api_videoconference + 'user/password_change', data, this.options)
    .then( r =>
      JSON.parse(r.data)
    ).catch( error => { console.log(error); });
  }
}
