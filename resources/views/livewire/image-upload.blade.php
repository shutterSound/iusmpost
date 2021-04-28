<div>
{{--    코드수정 => 입력부분--}}
    @if(session()->has('message'))
        <span class="text-danger">{{session('message')}}</span>
    @endif
    <form wire:submit.prevent ="saveimage" enctype="multipart/form-data">
        <input wire:model="searchDate" type="date" >

                                      {{-- 코드수정 => 원하는 화일 타입만 리스팅한다.--}}
        @if(!$fileSelected)
        <input wire:model="photos" type="file"  accept="image/x-png,image/gif,image/jpeg" multiple>
        @error('photos.*')<div class="text-danger">{{$message}}</div> @enderror
        @endif

        <button type="submit" >저장</button>
    </form>

{{--    코드수정 => 화일전달시 미리보기 처리하는 루틴  --}}
{{--    코드수정 => 미리보기에서는 임시폴더에서 삭제및 저장을 처리하며 저장을 누르면 원본화일이름으로 저장한다.  --}}
 @if($storeimages)
        @foreach($storeimages as $photo)
            <img src="{{ '/public/'.$photo }}" width="200px">
            {{--        코드수정 => 중요 사진보정한 사진만 다운로드 버튼이 출력이 되도록한다.--}}
            @if($pasDown[$loop->index] )
                <buutton wire:click.prevent="PasDownload({{$loop->index}})">
                    다운 로드
                </buutton>
            @endif
            <buutton wire:click.prevent="removeFileImage({{$loop->index}})">
            사진 삭제
            </buutton>
{{--       코드수정 => 중요 사진보정 실행하면 버튼숨김 --}}
            @if($pasProcess[$loop->index] )
            <buutton wire:click.prevent="PasProcess({{$loop->index}})">
                사진 보정
            </buutton>
            @endif
        @endforeach
{{--            코드수정 => 라이브의 임시폴더에 있는 것을 보여주는 방식 --}}
{{--    @foreach( $photos as $photo)--}}
{{--     <img src="{{ $photo->temporaryUrl() }}" width="200px">--}}
{{--        <buutton wire:click.prevent="removeImage({{$loop->index}})">--}}
{{--            삭제--}}
{{--        </buutton>--}}
{{--    @endforeach--}}
     @else
{{--  코드수정 => 디비에 저장된 필드를 읽어와서 목록식으로 출력처리하는 루틴
  삭제와 디비로 이미지 저장시 임시 폴더와 임시 화일들을 자동 삭제한다. 성공
--}}
      @foreach( $photorecords as $photo)
         <hr>
            <div wire:click="deleteRecord({{$photo->id}})">
                {{ $loop->index }}:{{$photo->id}} = {{count(explode(",", $photo->dataphoto))}}
            </div>
         @foreach(explode(",",$photo->dataphoto) as $file)
              @if(strlen($file) > 2)
                    @if(count(explode(",",$photo->dataphoto)) == 1 )
                        <a href="{{'/public/'.substr($file,2,-2)}}">
                        <img src="{{'/public/'.substr($file,2,-2)}}" width='100px'/>
                            {{substr(substr($file,2,-2), strpos(substr($file,2,-2), "--")+2) }}</a>
                    @elseif( ($loop->index + 1) == count(explode(",",$photo->dataphoto)) &&  ($loop->index + 1) > 1  )
                        <a href="{{'/public/'.substr($file,2,-2)}}">
                        <img src="{{'/public/'.substr($file,2,-2)}}" width='100px'/>
                            {{substr(substr($file,2,-2), strpos(substr($file,2,-2), "--")+2) }}</a>
                    @else
                        <a href="{{'/public/'.substr($file,2,-1)}}">
                        <img src="{{'/public/'.substr($file,2,-1)}}" width='100px'/>
                            {{substr(substr($file,2,-1), strpos(substr($file,2,-1), "--")+2) }}</a>
                    @endif
{{--                   코드수정 => 레코드 번호와 선택사진 인덱스를 전달하여 요소를 삭제한다.  --}}
                    <buutton wire:click.prevent="removeRecodeImage({{$photo->id}},{{$loop->index}})"
                    class="btn btn-primary">
                      삭제
                    </buutton>
              @endif
            @endforeach
      @endforeach
@endif
</div>
