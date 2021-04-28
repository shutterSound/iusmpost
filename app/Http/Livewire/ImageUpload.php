<?php
namespace App\Http\Livewire;

use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
class ImageUpload extends Component
{
    use WithFileUploads;
    public $photos = [];
    public $searchDate ;


    public $storeImages = [];
    public $folder = '';
    public $pasDown=[];
    public $pasProcess=[];

    public function mount()
    {
        // 코드수정 => 초기값 설정
        $this->searchDate = date('Y-m-d');
        $this->pasDown = array_fill(0,99,0);
        $this->pasProcess = array_fill(0,99,1);
    }

    public function PasProcess($index)
    {
//        코드수정 => 중요 다운로드 나타나게 함 .
        $this->pasDown[ $index ] = 1;
//        코드수정 => 중요 사진보정 사라지게 함 .
        $this->pasProcess[ $index ] = 0;
    }

//    코드수정 => 목록에서 삭제 버튼 누르면 레코드에서 이미지 삭제
    public function removeRecodeImage($recId , $index)
    {
//       코드수정 => 선택한 레코드를 찾는다.
       $record = Photo::find($recId);
//       코드수정 => 선택한 레코드의 화일 필드를 배열로 변환한다.
       $files =json_decode($record->dataphoto);

//       코드수정 =>    아래처럼 하면 선택된 배열요소 하나만 리턴한다.
//       배열을 문자열로 변환 = implode()
//        array_splice가 실행되면 files에는 지정위치의 값을 삭제한 나머지를 배열로 저장하며
//        deletefile에 삭제할 화일 배열요소를 문자열로 전달한다.
        $deletefile =  implode(array_splice($files, $index,1 ));

//         dd($deletefile, $files);
//        Storage::disk('public')->delete('public/'. $deletefile);

//        삭제가 정상 작동한다.
        @unlink('public/'. $deletefile);

//        코드수정 => 아래처럼 하면 선택한 배열요소를 삭제하고 나머지를 리턴한다.
//       array_splice($files, $index,1 );
//       코드수정 => 디비 레코드 갱신
       $record->update(['dataphoto' => $files]);
//       코드수정 =>선택화일 삭제


}
// 코드수정 => 미리보기에서 이미지 삭제
    public function removeImage($index)
    {
        array_splice($this->photos, $index,1 );
    }

    public function  saveimage()
    {
   //        코드수정 => 사용자 위치 지정
        $folder = Date('Y-m-d');
        $this->validate(
            [   'photos.*' => 'image|max:1024'],
            [ '화일크기가 너무 큽니다.']
        );

        foreach( $this->photos as $key => $photo ){
            //    코드수정 => 원본화이름을 가지고 공백을제거하고 유니크아이디를 부여하여 작업수행
            $fileName =  preg_replace("/\s+/","", $photo->getClientOriginalName());
            $fileName = uniqid().'--'.$fileName;
            $this->photos[$key] = $photo->storeAs($folder,$fileName, 'public');
        }
//    코드수정 => 다수의 화일이름들을 하나의 필드에 저장
        $this->photos = json_encode($this->photos);
        Photo::create(['dataphoto' => $this->photos]);
        session()->flash('message', '이미지저장!');
        $this->photos='';
        // 화일들을 레코드에 저장하면 임시폴더와 화일들을 삭제한다.
        $this->allDeleteFile($this->folder);
//        코드수정=> 리다이렉팅
        redirect()->to('/upload');
    }

    public function deleteRecord($id)
    {
        Photo::find($id) ->delete();
    }

    public function allDeleteFile($deletefolder)
    {
        // 코드수정 => 특정 폴더의 모든화일 삭제
        $deletes= Storage::disk('public')->files($deletefolder);
        foreach($deletes as $delete){
            @unlink('public/'. $delete);
        }
        // 폴더삭제
        Storage::deleteDirectory('public/'.$deletefolder);

    }

//    코드수정 => 화일에 저장된 이미지를 삭제함
    public function removeFileImage($index)
    {
//        코드수정 => 임시 폴더의 선택화일 삭제
        @unlink('public/'.$this->storeImages[$index] );
//        코드수정 => 화면정리
//        unset( $this->storeImages[$index] );
        array_splice($this->storeImages, $index,1 );
//        dd($this->storeImages);

//        코드수정 => 다운로드 버튼 다 숨김
//        $this->pasDown[ $index ] = 0;
        $this->pasDown= array_fill(0,count($this->storeImages),0);
//        코드수정 => 사진보정  버튼 다 나타냄
        $this->pasProcess = array_fill(0,count($this->storeImages),1);
//        코드수정 => 임시 폴더의 화일들을 다시읽어서 리스트 갱신
//        $this->storeImages = Storage::disk('public')->files($this->folder);
//         dd($this->storeImages[$index] , $this->folder);
    }

    // 코드수정 => 화일들을 선택하면 자동으로 실행되는 함수
    public function updatedphotos()
    {
//        dd($this->photos );
        // 코드수정 => 화일을선택하면 특정폴더에 먼저 저장하고 그폴더의 이미지들을 목록으로 전달한다.
        if( $this->photos ){
            $this->folder = uniqid().Date('Y-m-d');

            $this->validate(
                [   'photos.*' => 'image|max:1024'],
                [ '미리보기 => 화일크기가 너무 큽니다.']
            );
            foreach($this->photos as $key => $photo ){

                //    코드수정 => 원본화이름을 가지고 공백을제거하고 유니크아이디를 부여하여 작업수행
                $fileName =  preg_replace("/\s+/","", $photo->getClientOriginalName());
                $fileName = uniqid().'--'.$fileName;
                $photo->storeAs($this->folder,$fileName, 'public');
            }
//    코드수정 => 다수의 화일이름들을 하나의 필드에 저장
//            $this->photos = json_encode($this->photos);
            $this->storeImages = Storage::disk('public')->files($this->folder);
//            dd($this->storeImages );
        }

        $this->pasDown= array_fill(0,count($this->storeImages),0);
        $this->render();
    }
    public function render()
    {
        $photos = Photo::
        when($this->searchDate , function($query){
           $query-> where('created_at','like','%'.$this->searchDate.'%' );
        })
             ->latest()
             ->get();
        return view('livewire.image-upload',[
            'photorecords' => $photos,
            'storeimages'  => $this->storeImages
        ]);
    }
}
