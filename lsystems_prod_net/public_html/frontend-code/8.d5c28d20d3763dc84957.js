(window.webpackJsonp=window.webpackJsonp||[]).push([[8],{RyVU:function(t,e,n){"use strict";n.r(e);var c=n("Valr"),o=n("QJY3"),i=n("MnXN"),a=n("DUip"),r=n("JEAp"),l=n("cUzu"),b=n("AytR"),u=n("TYT/"),s=function(){function t(t,e){this.http=t,this.router=e,this.url=b.a.api_lscodegenerator+"projectattachment/",this.options={headers:null},this.options.headers=new l.c({api_token:sessionStorage.getItem("api_token")})}return t.prototype.get=function(t){var e=this;return void 0===t?this.http.get(this.url,this.options).toPromise().then((function(t){return t})).catch((function(t){e.handledError(t)})):this.http.get(this.url+"?id="+t.toString(),this.options).toPromise().then((function(t){return t})).catch((function(t){e.handledError(t)}))},t.prototype.get_paginate=function(t,e){var n=this;return this.http.get(this.url+"paginate?size="+t.toString()+"&page="+e.toString(),this.options).toPromise().then((function(t){return t})).catch((function(t){n.handledError(t)}))},t.prototype.delete=function(t){var e=this;return this.http.delete(this.url+"?id="+t.toString(),this.options).toPromise().then((function(t){return t})).catch((function(t){e.handledError(t)}))},t.prototype.getBackUp=function(){var t=this;return this.http.get(this.url+"backup",this.options).toPromise().then((function(t){return t})).catch((function(e){t.handledError(e)}))},t.prototype.post=function(t){var e=this;return this.http.post(this.url,JSON.stringify(t),this.options).toPromise().then((function(t){return t})).catch((function(t){e.handledError(t)}))},t.prototype.put=function(t){var e=this;return this.http.put(this.url,JSON.stringify(t),this.options).toPromise().then((function(t){return t})).catch((function(t){e.handledError(t)}))},t.prototype.masiveLoad=function(t){var e=this;return this.http.post(this.url+"masive_load",JSON.stringify({data:t}),this.options).toPromise().then((function(t){return t})).catch((function(t){e.handledError(t)}))},t.prototype.handledError=function(t){console.log(t),sessionStorage.clear(),this.router.navigate(["/login"])},t.\u0275fac=function(e){return new(e||t)(u.ac(l.a),u.ac(a.b))},t.\u0275prov=u.Jb({token:t,factory:t.\u0275fac,providedIn:"root"}),t}(),p=n("JYeP"),h=n("eALl"),f=n("s4wE");function g(t,e){1&t&&u.Ob(0,"span",40)}function d(t,e){if(1&t){var n=u.Tb();u.Sb(0,"tr",36),u.ec("click",(function(t){u.uc(n);var c=e.$implicit;return u.gc().selectProjectAttachment(c)})),u.Sb(1,"td",37),u.Bc(2,g,1,0,"span",38),u.Rb(),u.Sb(3,"td"),u.Dc(4),u.Rb(),u.Sb(5,"td"),u.Dc(6),u.Rb(),u.Sb(7,"td"),u.Dc(8),u.Rb(),u.Sb(9,"th"),u.Sb(10,"button",39),u.ec("click",(function(t){u.uc(n);var c=e.$implicit;return u.gc().downloadFile(c.project_attachment_file,c.project_attachment_file_type,c.project_attachment_file_name)})),u.Ob(11,"i",14),u.Rb(),u.Rb(),u.Rb()}if(2&t){var c=e.$implicit,o=u.gc();u.Ab(2),u.lc("ngIf",o.project_attachmentSelected===c),u.Ab(2),u.Ec(c.project_attachment_file_type),u.Ab(2),u.Ec(c.project_attachment_file_name),u.Ab(2),u.Ec(c.project_attachment_file)}}function m(t,e){1&t&&(u.Sb(0,"button",41),u.Dc(1,"Primera"),u.Rb())}function _(t,e){if(1&t){var n=u.Tb();u.Sb(0,"button",42),u.ec("click",(function(t){return u.uc(n),u.gc().goToPage(1)})),u.Dc(1,"Primera"),u.Rb()}}function S(t,e){if(1&t){var n=u.Tb();u.Sb(0,"button",43),u.ec("click",(function(t){u.uc(n);var e=u.gc();return e.goToPage(1*e.currentPage-1)})),u.Dc(1),u.Rb()}if(2&t){var c=u.gc();u.Ab(1),u.Ec(1*c.currentPage-1)}}function j(t,e){if(1&t){var n=u.Tb();u.Sb(0,"button",44),u.ec("click",(function(t){u.uc(n);var e=u.gc();return e.goToPage(1*e.currentPage+1)})),u.Dc(1),u.Rb()}if(2&t){var c=u.gc();u.Ab(1),u.Ec(1*c.currentPage+1)}}function v(t,e){if(1&t){var n=u.Tb();u.Sb(0,"button",45),u.ec("click",(function(t){u.uc(n);var e=u.gc();return e.goToPage(e.lastPage)})),u.Dc(1,"\xdaltima"),u.Rb()}}function y(t,e){1&t&&(u.Sb(0,"button",46),u.Dc(1,"\xdaltima"),u.Rb())}function P(t,e){if(1&t&&(u.Sb(0,"option",66),u.Dc(1),u.Rb()),2&t){var n=e.$implicit;u.mc("value",n.id),u.Ab(1),u.Fc(" ",n.id," ")}}function R(t,e){if(1&t){var n=u.Tb();u.Sb(0,"div",47),u.Sb(1,"h4",48),u.Dc(2,"Datos:"),u.Rb(),u.Sb(3,"button",49),u.ec("click",(function(t){return e.$implicit.dismiss("Cross click")})),u.Sb(4,"span"),u.Dc(5,"\xd7"),u.Rb(),u.Rb(),u.Rb(),u.Sb(6,"div",50),u.Sb(7,"div",0),u.Sb(8,"div",2),u.Sb(9,"div",51),u.Sb(10,"label",52),u.Dc(11,"project_attachment_file_type"),u.Rb(),u.Sb(12,"div",53),u.Sb(13,"input",54),u.ec("ngModelChange",(function(t){return u.uc(n),u.gc().project_attachmentSelected.project_attachment_file_type=t})),u.Rb(),u.Rb(),u.Rb(),u.Sb(14,"div",51),u.Sb(15,"label",55),u.Dc(16,"project_attachment_file_name"),u.Rb(),u.Sb(17,"div",53),u.Sb(18,"input",56),u.ec("ngModelChange",(function(t){return u.uc(n),u.gc().project_attachmentSelected.project_attachment_file_name=t})),u.Rb(),u.Rb(),u.Rb(),u.Sb(19,"div",51),u.Sb(20,"label",57),u.Dc(21,"project_attachment_file"),u.Rb(),u.Sb(22,"div",53),u.Sb(23,"input",58),u.ec("change",(function(t){return u.uc(n),u.gc().CodeFileProjectAttachment(t)})),u.Rb(),u.Rb(),u.Rb(),u.Sb(24,"div",51),u.Sb(25,"label",59),u.Dc(26,"Project"),u.Rb(),u.Sb(27,"div",53),u.Sb(28,"select",60),u.ec("ngModelChange",(function(t){return u.uc(n),u.gc().project_attachmentSelected.project_id=t})),u.Sb(29,"option",61),u.Dc(30,"Seleccione..."),u.Rb(),u.Bc(31,P,2,2,"option",62),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Sb(32,"div",63),u.Sb(33,"button",64),u.ec("click",(function(t){return e.$implicit.close("Guardar click")})),u.Dc(34,"Guardar"),u.Rb(),u.Sb(35,"button",65),u.ec("click",(function(t){return e.$implicit.close("Cancelar click")})),u.Dc(36,"Cancelar"),u.Rb(),u.Rb()}if(2&t){var c=u.gc();u.Ab(13),u.lc("ngModel",c.project_attachmentSelected.project_attachment_file_type),u.Ab(5),u.lc("ngModel",c.project_attachmentSelected.project_attachment_file_name),u.Ab(10),u.lc("ngModel",c.project_attachmentSelected.project_id),u.Ab(3),u.lc("ngForOf",c.projects)}}var k=[{path:"",component:function(){function t(t,e,n,c){this.modalService=t,this.toastr=e,this.projectDataService=n,this.project_attachmentDataService=c,this.project_attachments=[],this.project_attachmentSelected=new p.a,this.currentPage=1,this.lastPage=1,this.showDialog=!1,this.recordsByPage=5,this.projects=[]}return t.prototype.ngOnInit=function(){this.goToPage(1),this.getProject()},t.prototype.CodeFileProjectAttachment=function(t){var e=this,n=new FileReader;if(t.target.files&&t.target.files.length>0){var c=t.target.files[0];n.readAsDataURL(c),n.onload=function(){e.project_attachmentSelected.project_attachment_file_name=c.name,e.project_attachmentSelected.project_attachment_file_type=c.type,e.project_attachmentSelected.project_attachment_file=n.result.toString().split(",")[1]}}},t.prototype.selectProjectAttachment=function(t){this.project_attachmentSelected=t},t.prototype.getProject=function(){var t=this;this.projects=[],this.projectDataService.get().then((function(e){t.projects=e})).catch((function(t){return console.log(t)}))},t.prototype.goToPage=function(t){t<1||t>this.lastPage?this.toastr.errorToastr("La p\xe1gina solicitada no existe.","Error"):(this.currentPage=t,this.getProjectAttachments())},t.prototype.getProjectAttachments=function(){var t=this;this.project_attachments=[],this.project_attachmentSelected=new p.a,this.project_attachmentSelected.project_id=0,this.project_attachmentDataService.get_paginate(this.recordsByPage,this.currentPage).then((function(e){t.project_attachments=e.data,t.lastPage=e.last_page})).catch((function(t){return console.log(t)}))},t.prototype.newProjectAttachment=function(t){this.project_attachmentSelected=new p.a,this.project_attachmentSelected.project_id=0,this.openDialog(t)},t.prototype.editProjectAttachment=function(t){void 0!==this.project_attachmentSelected.id?this.openDialog(t):this.toastr.errorToastr("Debe seleccionar un registro.","Error")},t.prototype.deleteProjectAttachment=function(){var t=this;void 0!==this.project_attachmentSelected.id?this.project_attachmentDataService.delete(this.project_attachmentSelected.id).then((function(e){t.toastr.successToastr("Registro Borrado satisfactoriamente.","Borrar"),t.getProjectAttachments()})).catch((function(t){return console.log(t)})):this.toastr.errorToastr("Debe seleccionar un registro.","Error")},t.prototype.backup=function(){this.project_attachmentDataService.getBackUp().then((function(t){var e=new Blob([JSON.stringify(t)],{type:"text/plain"}),n=new Date;Object(r.saveAs)(e,n.toLocaleDateString()+"_ProjectAttachments.json")})).catch((function(t){return console.log(t)}))},t.prototype.toCSV=function(){this.project_attachmentDataService.get().then((function(t){var e="id;project_attachment_file_type;project_attachment_file_name;project_attachment_file;project_id\n";t.forEach((function(t){e+=t.id+";"+t.project_attachment_file_type+";"+t.project_attachment_file_name+";"+t.project_attachment_file+";"+t.project_id+"\n"}));var n=new Blob([e],{type:"text/plain"}),c=new Date;Object(r.saveAs)(n,c.toLocaleDateString()+"_ProjectAttachments.csv")})).catch((function(t){return console.log(t)}))},t.prototype.decodeUploadFile=function(t){var e=this,n=new FileReader;t.target.files&&t.target.files.length>0&&(n.readAsDataURL(t.target.files[0]),n.onload=function(){var t=n.result.toString().split(",")[1],c=JSON.parse(decodeURIComponent(escape(atob(t))));e.project_attachmentDataService.masiveLoad(c).then((function(t){e.goToPage(e.currentPage)})).catch((function(t){return console.log(t)}))})},t.prototype.downloadFile=function(t,e,n){for(var c=atob(t),o=new Array(c.length),i=0;i<c.length;i++)o[i]=c.charCodeAt(i);var a=new Uint8Array(o),l=new Blob([a],{type:e});Object(r.saveAs)(l,n)},t.prototype.openDialog=function(t){var e=this;this.modalService.open(t,{centered:!0,size:"lg"}).result.then((function(t){"Guardar click"===t&&(void 0===e.project_attachmentSelected.id?e.project_attachmentDataService.post(e.project_attachmentSelected).then((function(t){e.toastr.successToastr("Datos guardados satisfactoriamente.","Nuevo"),e.getProjectAttachments()})).catch((function(t){return console.log(t)})):e.project_attachmentDataService.put(e.project_attachmentSelected).then((function(t){e.toastr.successToastr("Registro actualizado satisfactoriamente.","Actualizar"),e.getProjectAttachments()})).catch((function(t){return console.log(t)})))}),(function(t){}))},t.\u0275fac=function(e){return new(e||t)(u.Nb(i.e),u.Nb(f.a),u.Nb(h.a),u.Nb(s))},t.\u0275cmp=u.Hb({type:t,selectors:[["app-projectattachment"]],decls:63,vars:11,consts:[[1,"row"],[1,"col-12","text-right"],[1,"col-12"],["role","toolbar",1,"btn-toolbar"],["role","group",1,"btn-group","mr-2"],["type","button","title","Actualizar",1,"btn","btn-primary",3,"click"],[1,"fas","fa-sync"],["type","button","title","Nuevo",1,"btn","btn-success",3,"click"],[1,"fas","fa-file"],["type","button","title","Editar",1,"btn","btn-warning",3,"click"],[1,"fas","fa-edit"],["type","button","title","Eliminar",1,"btn","btn-danger",3,"click"],[1,"fas","fa-trash"],["type","button","title","BackUp",1,"btn","btn-dark",3,"click"],[1,"fas","fa-download"],["type","button","title","Exportar CSV",1,"btn","btn-dark",3,"click"],[1,"fas","fa-file-csv"],["type","button","title","Cargar",1,"btn","btn-dark",3,"click"],[1,"fas","fa-upload"],["type","file","accept",".json",1,"form-control",3,"hidden","change"],["uploadInput",""],[1,"table","table-hover","mt-2"],[3,"click",4,"ngFor","ngForOf"],["type","button","class","btn btn-light","title","Primera P\xe1gina","disabled","",4,"ngIf"],["type","button","class","btn btn-light","title","Primera P\xe1gina",3,"click",4,"ngIf"],["type","button","class","btn btn-light","title","P\xe1gina Anterior",3,"click",4,"ngIf"],["type","button","title","P\xe1gina Actual",1,"btn","btn-primary"],["type","button","class","btn btn-light","title","P\xe1gina Siguiente",3,"click",4,"ngIf"],["type","button","class","btn btn-light","title","\xdaltima P\xe1gina",3,"click",4,"ngIf"],["type","button","class","btn btn-light","title","\xdaltima P\xe1gina","disabled","",4,"ngIf"],[1,"input-group"],[1,"input-group-prepend"],["type","button","title","Ir a la P\xe1gina",1,"input-group-text","btn","btn-success",3,"click"],["type","number","placeholder","Ir a la P\xe1gina",1,"form-control",3,"min","max"],["goToPageNumber",""],["content",""],[3,"click"],[1,"text-right"],["class","far fa-hand-point-right",4,"ngIf"],["type","button","title","Descargar",1,"btn","btn-success",3,"click"],[1,"far","fa-hand-point-right"],["type","button","title","Primera P\xe1gina","disabled","",1,"btn","btn-light"],["type","button","title","Primera P\xe1gina",1,"btn","btn-light",3,"click"],["type","button","title","P\xe1gina Anterior",1,"btn","btn-light",3,"click"],["type","button","title","P\xe1gina Siguiente",1,"btn","btn-light",3,"click"],["type","button","title","\xdaltima P\xe1gina",1,"btn","btn-light",3,"click"],["type","button","title","\xdaltima P\xe1gina","disabled","",1,"btn","btn-light"],[1,"modal-header"],[1,"modal-title"],["type","button",1,"close",3,"click"],[1,"modal-body"],[1,"form-group","row"],["for","project_attachment_file_type",1,"col-4","col-form-label"],[1,"col-8"],["type","text","id","project_attachment_file_type","name","project_attachment_file_type","placeholder","ProjectAttachmentFileType",1,"form-control",3,"ngModel","ngModelChange"],["for","project_attachment_file_name",1,"col-4","col-form-label"],["type","text","id","project_attachment_file_name","name","project_attachment_file_name","placeholder","ProjectAttachmentFileName",1,"form-control",3,"ngModel","ngModelChange"],["for","project_attachment_file",1,"col-4","col-form-label"],["type","file","id","project_attachment_file","name","project_attachment_file","placeholder","ProjectAttachmentFile",1,"form-control",3,"change"],["for","project_id",1,"col-4","col-form-label"],["id","project_id","name","project_id",1,"form-control",3,"ngModel","ngModelChange"],["value","0","selected",""],[3,"value",4,"ngFor","ngForOf"],[1,"modal-footer"],["type","button",1,"btn","btn-outline-success",3,"click"],["type","button",1,"btn","btn-outline-danger",3,"click"],[3,"value"]],template:function(t,e){if(1&t){var n=u.Tb();u.Sb(0,"div",0),u.Sb(1,"h1",1),u.Dc(2," ProjectAttachment "),u.Rb(),u.Rb(),u.Sb(3,"div",0),u.Sb(4,"div",2),u.Sb(5,"div",3),u.Sb(6,"div",4),u.Sb(7,"button",5),u.ec("click",(function(t){return e.goToPage(e.currentPage)})),u.Ob(8,"i",6),u.Rb(),u.Rb(),u.Sb(9,"div",4),u.Sb(10,"button",7),u.ec("click",(function(t){u.uc(n);var c=u.tc(62);return e.newProjectAttachment(c)})),u.Ob(11,"i",8),u.Rb(),u.Sb(12,"button",9),u.ec("click",(function(t){u.uc(n);var c=u.tc(62);return e.editProjectAttachment(c)})),u.Ob(13,"i",10),u.Rb(),u.Rb(),u.Sb(14,"div",4),u.Sb(15,"button",11),u.ec("click",(function(t){return e.deleteProjectAttachment()})),u.Ob(16,"i",12),u.Rb(),u.Rb(),u.Sb(17,"div",4),u.Sb(18,"button",13),u.ec("click",(function(t){return e.backup()})),u.Ob(19,"i",14),u.Rb(),u.Sb(20,"button",15),u.ec("click",(function(t){return e.toCSV()})),u.Ob(21,"i",16),u.Rb(),u.Sb(22,"button",17),u.ec("click",(function(t){return u.uc(n),u.tc(25).click()})),u.Ob(23,"i",18),u.Rb(),u.Sb(24,"input",19,20),u.ec("change",(function(t){return e.decodeUploadFile(t)})),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Sb(26,"div",0),u.Sb(27,"div",2),u.Sb(28,"table",21),u.Sb(29,"thead"),u.Sb(30,"tr"),u.Sb(31,"th"),u.Dc(32,"Seleccionado"),u.Rb(),u.Sb(33,"th"),u.Dc(34,"project_attachment_file_type"),u.Rb(),u.Sb(35,"th"),u.Dc(36,"project_attachment_file_name"),u.Rb(),u.Sb(37,"th"),u.Dc(38,"project_attachment_file"),u.Rb(),u.Sb(39,"th"),u.Dc(40,"Opciones"),u.Rb(),u.Rb(),u.Rb(),u.Sb(41,"tbody"),u.Bc(42,d,12,4,"tr",22),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Sb(43,"div",0),u.Sb(44,"div",2),u.Sb(45,"div",3),u.Sb(46,"div",4),u.Bc(47,m,2,0,"button",23),u.Bc(48,_,2,0,"button",24),u.Bc(49,S,2,1,"button",25),u.Sb(50,"button",26),u.Dc(51),u.Rb(),u.Bc(52,j,2,1,"button",27),u.Bc(53,v,2,0,"button",28),u.Bc(54,y,2,0,"button",29),u.Rb(),u.Sb(55,"div",30),u.Sb(56,"div",31),u.Sb(57,"button",32),u.ec("click",(function(t){u.uc(n);var c=u.tc(60);return e.goToPage(c.value)})),u.Dc(58,"Ir a"),u.Rb(),u.Rb(),u.Ob(59,"input",33,34),u.Rb(),u.Rb(),u.Rb(),u.Rb(),u.Bc(61,R,37,4,"ng-template",null,35,u.Cc)}2&t&&(u.Ab(24),u.lc("hidden",!0),u.Ab(18),u.lc("ngForOf",e.project_attachments),u.Ab(5),u.lc("ngIf",1===e.currentPage),u.Ab(1),u.lc("ngIf",1!==e.currentPage),u.Ab(1),u.lc("ngIf",e.currentPage>1),u.Ab(2),u.Ec(e.currentPage),u.Ab(1),u.lc("ngIf",e.currentPage<e.lastPage),u.Ab(1),u.lc("ngIf",e.currentPage!==e.lastPage),u.Ab(1),u.lc("ngIf",e.currentPage===e.lastPage),u.Ab(5),u.mc("min",1),u.mc("max",e.lastPage))},directives:[c.k,c.l,o.b,o.f,o.i,o.l,o.j,o.m],styles:[""]}),t}()}],A=function(){function t(){}return t.\u0275mod=u.Lb({type:t}),t.\u0275inj=u.Kb({factory:function(e){return new(e||t)},imports:[[a.f.forChild(k)],a.f]}),t}();n.d(e,"ProjectAttachmentModule",(function(){return D}));var D=function(){function t(){}return t.\u0275mod=u.Lb({type:t}),t.\u0275inj=u.Kb({factory:function(e){return new(e||t)},providers:[i.e,h.a,s],imports:[[c.b,A,o.c]]}),t}()}}]);