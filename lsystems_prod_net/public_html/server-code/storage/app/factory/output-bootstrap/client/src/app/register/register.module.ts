import { AuthService } from './../services/auth.service';
import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';

import { RegisterRoutingModule } from './register-routing.module';
import { RegisterComponent } from './register.component';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

@NgModule({
    imports: [CommonModule, RegisterRoutingModule, FormsModule, HttpClientModule],
    declarations: [RegisterComponent],
    providers: [AuthService]
})
export class RegisterModule {}
