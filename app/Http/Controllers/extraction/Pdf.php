<?php

namespace App\Http\Controllers\extraction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Smalot\PdfParser\Parser;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\DocumentModel;
use App\Models\User;
use Spatie\PdfToImage\Pdf as PdfToImage;
use Imagick;

set_time_limit(120);
class Pdf extends Controller
{
    
    //

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,png,bmp,gif|max:2048',
        ]);

        $uploadFile = $request->file('file');
        $pdfName = $uploadFile->getClientOriginalName();
        $fileSize = $uploadFile->getSize();
        $uploadFile->move(public_path('file'), $pdfName);
        $fileUploaded = public_path('file').'/'.$pdfName;

        // Set the path to Ghostscript executable using Imagick
        putenv('MAGICK_HOME=C:\Program Files\gs\gs10.04.0');
        putenv('PATH=' . getenv('PATH') . ';C:\Program Files\gs\gs10.04.0\bin');
        $pdfToImage = new PdfToImage($fileUploaded);
        $pdfToImage->setResolution(300);

        for ($i = 1; $i <= $pdfToImage->getNumberOfPages(); $i++) {
            $pdfToImage->setPage($i)->saveImage(public_path('file/converted').'/'.$pdfName.'-'.$i.'.png');
        }

        // Set the path to Tesseract executable
        $ocrResults = [];
        for ($i = 1; $i <= $pdfToImage->getNumberOfPages(); $i++) {
            $ocr = (new TesseractOCR(public_path('file/converted').'/'.$pdfName.'-'.$i.'.png'))
                ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
                ->lang('ind', 'eng')
                ->run();
            $ocrResults[] = [
                'page' => $i,
                'text' => $ocr,
            ];
        }

        $saveDocument = new DocumentModel();
        $saveDocument->name = $pdfName;
        $saveDocument->path = $pdfName;
        $saveDocument->type = $uploadFile->getClientMimeType();
        $saveDocument->size = $fileSize;
        $saveDocument->extension = $uploadFile->getClientOriginalExtension();
        $saveDocument->mime_type = $uploadFile->getClientMimeType();
        $saveDocument->content = json_encode($ocrResults);
        $saveDocument->save();

        return response()->json([
            'status' => true,
            'message' => 'PDF uploaded successfully',
            'data' => [
                'text' => $ocrResults,
            ]
        ]);
    }
 
    
    public function searchDocument(Request $request) {
        $document = DocumentModel::search(trim($request->get('search')) ?? '',function($meiliSearch,string $query ,array $options){
            $options['attributesToHighlight'] = ['content','name'];
            return $meiliSearch->search($query,$options);
        })->paginateRaw(2);
        return response()->json($document);
    }

    public function searchUser(Request $request){
        $user = User::search(trim($request->get('search')) ?? '')->get();
        return response()->json($user);
    }
}
