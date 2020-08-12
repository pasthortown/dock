<?php

namespace App\Http\Controllers;

class ComponentSpecTemplateController extends Controller
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

    static function build($args) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $content = "import { async, ComponentFixture, TestBed } from '@angular/core/testing';\n";
        $content .= "import { ".$tableNameSingular."Component } from './".strtolower($tableNameSingular).".component';\n\n";
        $content .= "describe('".$tableNameSingular."Component', () => {\n";
        $content .= "   let component: ".$tableNameSingular."Component;\n";
        $content .= "   let fixture: ComponentFixture<".$tableNameSingular."Component>;\n\n";
        $content .= "   beforeEach(async(() => {\n";
        $content .= "      TestBed.configureTestingModule({\n";
        $content .= "         declarations: [".$tableNameSingular."Component]\n";
        $content .= "      }).compileComponents();\n";
        $content .= "   }));\n\n";
        $content .= "   beforeEach(() => {\n";
        $content .= "      fixture = TestBed.createComponent(".$tableNameSingular."Component);\n";
        $content .= "      component = fixture.componentInstance;\n";
        $content .= "      fixture.detectChanges();\n";
        $content .= "   });\n\n";
        $content .= "   it('should create', () => {\n";
        $content .= "      expect(component).toBeTruthy();\n";
        $content .= "   });\n";
        $content .= "});";
        return $content;
    }
}
