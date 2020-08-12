import { Component, OnInit } from '@angular/core';
import { AuthService } from './../services/auth.service';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {

  password = '';
  email = '';
  busy: Promise<any>;
  esperando: boolean;

  constructor(
    private toastController: ToastController,
    private authDataServise: AuthService,
    private router: Router) { }

  ngOnInit() {
    this.email = '';
    this.password = '';
    this.esperando = false;
  }

  login() {
    if ( !this.esperando ) {
      this.esperando = true;
      this.busy = this.authDataServise.login(this.email, this.password).then( r => {
        const response = r;
        this.esperando = false;
        sessionStorage.setItem('api_token', response.token);
        sessionStorage.setItem('isLoggedin', 'true');
        const userData = { id: response.id, name: response.name };
        sessionStorage.setItem('user', JSON.stringify(userData));
        this.router.navigate(['/main']);
      }).catch( e => {
        this.esperando = false;
        this.presentToastWithOptions('Credenciales Incorrectos', 'middle', 'Aceptar', 'danger','Error','close-outline','start').then(
          r => {
            r.onDidDismiss().then( r2 => {
              sessionStorage.clear();
              this.router.navigate(['/login']);
            });
          }
        );
      });
    }
  }

  password_recovery() {
    if ( !this.esperando ) {
      this.esperando = true;
      this.busy = this.authDataServise.password_recovery_request(this.email).then( r => {
        this.esperando = false;
        const response = r;
        if ( response === 'Success!') {
          this.presentToastWithOptions('Para completar el proceso, revisa tu correo', 'middle', 'Aceptar', 'success','Guardado','save-outline','start').then(
            r_success => {
              r_success.onDidDismiss().then( r2 => {
                this.password = '';
                this.email = '';
              });
            }
          );
        } else {
          this.presentToastWithOptions('La dirección de correo proporcionada, no corresponde a cuenta alguna', 'middle', 'Aceptar', 'warning','Error','close-outline','start')
          .then(
            r_fail => {
              r_fail.onDidDismiss().then( r2 => {
                this.password = '';
                this.email = '';
              });
            }
          );
        }
      }).catch( e => {
        console.log(e);
        this.esperando = false;
      });
    }
  }

  async presentToastWithOptions(message, position, closeButtonText, color, title, buttonIcon, buttonSide) {
    const toast = await this.toastController.create({
      header: title,
      message: message,
      position: position,
      buttons: [
        {
          side: buttonSide,
          icon: buttonIcon,
          text: closeButtonText,
          handler: () => {

          }
        }
      ]
    });
    toast.present();
    return toast;
  }
}
