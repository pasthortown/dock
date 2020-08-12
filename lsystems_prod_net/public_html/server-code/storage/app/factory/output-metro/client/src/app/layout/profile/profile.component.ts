import { Router } from '@angular/router';
import { Component, OnInit, ViewChild } from '@angular/core';
import Swal from 'sweetalert2';
import { AuthService } from 'src/app/services/auth.service';
import { ProfilePictureService } from 'src/app/services/profile/profilepicture.service';
import { UserService } from 'src/app/services/profile/user.service';
import { ProfilePicture } from 'src/app/models/profile/ProfilePicture';
import { User } from 'src/app/models/profile/User';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {
  cambiandoClaves = false;
  clavesCoinciden = false;
  clave: String = '';
  claveConfirm: String = '';
  profileImg = 'assets/images/accounts.png';
  profilePicture: ProfilePicture;
  user: User;
  @ViewChild('fotoInput') fotoInput;

  constructor(
    private router: Router,
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

  subirFoto() {
    this.fotoInput.nativeElement.click();
  }

  CodificarArchivo(event) {
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

  actualizarFoto() {
    this.profilePictureDataService.put(this.profilePicture).then( r => {
      this.finCambios();
    }).catch( e => console.log(e) );
  }

  finCambios() {
    if (this.cambiandoClaves) {
      Swal.fire({
        title: 'Datos Guardados',
        text: 'Datos guardados satisfactoriamente. Utilice su nueva contraseña, para iniciar sesión.',
        type: 'success',
        showCancelButton: false,
        confirmButtonText: 'De acuerdo',
        reverseButtons: true
      }).then((result) => {
        this.router.navigate(['/login']);
      });
    } else {
      Swal.fire({
        title: 'Datos Guardados',
        text: 'Datos guardados satisfactoriamente.',
        type: 'success',
        showCancelButton: false,
        confirmButtonText: 'De acuerdo',
        reverseButtons: true
      }).then((result) => {
        window.location.reload();
      });
    }
  }

  actualizarClave() {
    this.authDataServise.password_change(this.clave).then( r => {
      this.guardarFoto();
    }).catch( e => {
      console.log(e);
    });
  }
}
