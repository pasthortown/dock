import { Router } from '@angular/router';
import { AuthService } from './../../services/auth.service';
import { Component, OnInit, ViewChild } from '@angular/core';
import { UserService } from 'src/app/services/profile/user.service';
import { User } from 'src/app/models/profile/User';
import { ToastController } from '@ionic/angular';
import { Camera, CameraOptions } from '@ionic-native/camera/ngx';
import { ProfilePicture } from 'src/app/models/profile/ProfilePicture';
import { ProfilePictureService } from 'src/app/services/profile/profilepicture.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.page.html',
  styleUrls: ['./profile.page.scss'],
})
export class ProfilePage implements OnInit {
  @ViewChild('fotoInput', {static: false}) fotoInput;
  cambiandoClaves = false;
  clavesCoinciden = false;
  clave: String = '';
  claveConfirm: String = '';
  user: User;
  profileImg = 'assets/images/accounts.png';
  profilePicture: ProfilePicture;
  appName = 'APPNAME';

  constructor(
    private camera: Camera,
    public router: Router,
    private toastController: ToastController,
    private authDataServise: AuthService,
    private profilePictureDataService: ProfilePictureService,
    private userDataService: UserService) {
    this.user = new User();
    this.profilePicture = new ProfilePicture();
  }

  ngOnInit() {
    this.getUser();
  }

  getUser() {
    this.userDataService.get(JSON.parse(sessionStorage.getItem('user')).id).then( r => {
      this.user = r as User;
      this.getProfilePicture();
    }).catch( e => console.log(e));
  }

  getProfilePicture() {
    this.profileImg = 'assets/images/accounts.png';
    this.profilePictureDataService.get(this.user.id).then( r2 => {
      const response = r2;
      if (typeof response.id !== 'undefined') {
        this.profilePicture = r2 as ProfilePicture;
        if (this.profilePicture.id !== 0) {
          this.profileImg = 'data:' + this.profilePicture.file_type + ';base64,' + this.profilePicture.file;
        }
      }
    }).catch( e => { console.log(e); });
  }


  verificarCambioClaves() {
    if (this.clave.length !== 0 || this.claveConfirm.length !== 0) {
      this.cambiandoClaves = true;
    } else {
      this.cambiandoClaves = false;
    }
    if (this.clave === this.claveConfirm) {
      this.clavesCoinciden = true;
    } else {
      this.clavesCoinciden = false;
    }
  }

  guardar() {
    const userData = { id: this.user.id, name: this.user.name };
    sessionStorage.setItem('user', JSON.stringify(userData));
    this.userDataService.put(this.user).then( r => {
      if (this.cambiandoClaves && this.clavesCoinciden) {
        this.actualizarClave();
      } else {
        this.guardarFoto();
      }
    }).catch ( e => console.log(e));
  }

  guardarFoto() {
    let actualizando_foto = true;
    if ( this.profileImg === 'assets/images/accounts.png' ) {
      actualizando_foto = false;
    }
    if (actualizando_foto) {
      if (this.profilePicture.id === 0 ) {
        this.profilePictureDataService.post(this.profilePicture).then( r => {
          this.finCambios();
        }).catch( e => console.log(e) );
      } else {
        this.actualizarFoto();
      }
    } else {
      this.finCambios();
    }
  }

  finCambios() {
    if (this.cambiandoClaves) {
      this.presentToastWithOptions('Datos guardados satisfactoriamente. Utilice su nueva contraseña, para iniciar sesión.', 'middle', 'Aceptar', 'success','Guardado','save-outline','start').then(
        r_success => {
          r_success.onDidDismiss().then( r2 => {
            this.router.navigate(['/login']);
          });
        }
      );
    } else {
      this.presentToastWithOptions('Datos guardados satisfactoriamente.', 'middle', 'Aceptar', 'success','Guardado','save-outline','start').then(
        r_success => {
          r_success.onDidDismiss().then( r2 => {
            window.location.reload();
          });
        }
      );
    }
  }

  actualizarFoto() {
    this.profilePictureDataService.put(this.profilePicture).then( r => {
      sessionStorage.setItem('profilePicture', JSON.stringify(this.profilePicture));
      this.profileImg = 'data:' + r.file_type + ';base64,' + r.file;
    }).catch( e => console.log(e) );
  }

  actualizarClave() {
    this.authDataServise.password_change(this.clave).then( r => {
      this.presentToastWithOptions('Datos guardados satisfactoriamente. Cierre sesión y utilice su nueva contraseña.', 'middle', 'Aceptar', 'success','Guardado','save-outline','start').then(
        r_success => {
          r_success.onDidDismiss().then( r2 => {
          });
        }
      );
    }).catch( e => {
      console.log(e);
    });
  }

  getPicture(): void {
    if (Camera['installed']()) {
      const options: CameraOptions = {
        quality: 100,
        destinationType: this.camera.DestinationType.DATA_URL,
        encodingType: this.camera.EncodingType.JPEG,
        mediaType: this.camera.MediaType.PICTURE,
        correctOrientation: true,
        allowEdit: true
      };
      this.camera.getPicture(options).then((imageData) => {
        this.profilePicture.file_name = 'foto_desde_camara.jpg';
        this.profilePicture.file_type = 'image/jpeg';
        this.profilePicture.file = imageData;
        this.profileImg = 'data:' + this.profilePicture.file_type + ';base64,' + this.profilePicture.file;
       }, (err) => {
        this.profileImg = 'assets/images/accounts.png';
      });
    } else {
      this.desdeAlmacenamiento();
    }
  }

  desdeAlmacenamiento() {
    this.fotoInput.nativeElement.click();
  }

  subirImagen(event) {
    const reader = new FileReader();
    if (event.target.files && event.target.files.length > 0) {
      const file = event.target.files[0];
      reader.readAsDataURL(file);
      reader.onload = () => {
        this.profilePicture.file_name = file.name;
        this.profilePicture.file_type = file.type;
        this.profilePicture.file = reader.result.toString().split(',')[1];
        this.profileImg = 'data:' + this.profilePicture.file_type + ';base64,' + this.profilePicture.file;
      };
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
