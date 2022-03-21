<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Spiritix\Html2Pdf\Converter;
use Spiritix\Html2Pdf\Input\StringInput;
use Spiritix\Html2Pdf\Output\StringOutput;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware( 'auth:sanctum' )->get( '/user' , function ( Request $request )
{
    return $request->user();
} )
;

/*
printBackground <[boolean]> Print background graphics. Defaults to false.
format <[string]> Paper format. If set, takes priority over width or height options. Defaults to 'Letter'.
width <[string]> Paper width, accepts values labeled with units.
height <[string]> Paper height, accepts values labeled with units.
mediaType <?[string]> Changes the CSS media type of the page. The only allowed values are 'screen', 'print' and null. Passing null disables media emulation.
pageWaitFor <[integer]> Timeout in milliseconds to wait for.
cookies<[array]> Cookie objects to set.
NOTE headerTemplate and footerTemplate markup have the following limitations:

Script tags inside templates are not evaluated.
Page styles are not visible inside templates.
NOTE By default, this library generates a pdf with modified colors for printing. Use the -webkit-print-color-adjust property to force rendering of exact colors.

 */
Route::any( 'html' , static function ( Request $request )
{

    $input = new StringInput();
    $input->setHtml(
        '<!DOCTYPE html><html lang="es"><head><title></title></head>'
        . $request->get( 'body' )
        . '</html>'
    );

    $converter = new Converter( $input , new StringOutput() );

    $converter->setOptions( [
        'printBackground' => true ,
        'format'          => 'A4' , // Takes precedence over width, height
        'width'           => '210mm' ,
        'height'          => '297mm' ,
    ] );

    /** @var StringOutput $output */
    $output = $converter->convert();

    return response()->make(
        $output->get() ,
        Response::HTTP_OK ,
        [
            'Content-Type' => 'application/pdf' ,
            'Content-Disposition' => 'aattachment;filename="facturas.pdf"' ,
        ]
    );
} );
