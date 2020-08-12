import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';

import { LayoutRoutingModule } from './layout-routing.module';
import { LayoutComponent } from './layout.component';
import { NavbarComponent } from '../components/navbar/navbar.component';
import { SidebarComponent } from '../components/sidebar/sidebar.component';
import { NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';

import { UserService } from 'src/app/services/profile/user.service';
import { ProfilePictureService } from 'src/app/services/profile/profilepicture.service';

@NgModule({
    imports: [CommonModule, LayoutRoutingModule, NgbDropdownModule, HttpClientModule],
    declarations: [LayoutComponent, NavbarComponent, SidebarComponent],
    providers: [ProfilePictureService, UserService]
})
export class LayoutModule {}
