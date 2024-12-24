<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('extract.search-user') }}" method="GET" style="margin-bottom:10px ">
                <div class="flex items-center">
                    <input type="text" name="search" id="search-form" class="w-full border-2 rounded-lg p-2" placeholder="Search use">
                </div>
            </form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form action="/file-upload" class="dropzone" id="my-awesome-dropzone"></form>
                    </div>
                    <button class="px-4 py-2 bg-blue-500 text-dark font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                        Extract PDF <i class="fa fa-syncs"></i>
                    </button>
                </div>
                <div>
                    <textarea name="textarea" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" id="convert-result" cols="30" rows="10"></textarea>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function () {
            $('#search-form').autocomplete({
            source: function (request, response) {
                $.ajax({
                url: '{{ route('extract.search-document') }}',
                type: 'GET',
                data: {
                    search: request.term
                },
                success: function (responses) {
                    response( responses.data.hits.map(function(item) {
                     return {
                        label: item.content,
                        value: item.name,
                        id: item.id
                     }
                  }));
                }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(ui.item);
            }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
           const dropzoneElement = document.querySelector("#my-awesome-dropzone");
           const myDropzone = new Dropzone(dropzoneElement, {
               url: "{{url('extract/pdf')}}",
               headers:{
                   'X-CSRF-TOKEN': "{{ csrf_token() }}"
               },
               paramName: "file",
               maxFilesize: 2, // Ukuran file maksimum dalam MB
               acceptedFiles: ".pdf,.png,.jpg,.jpeg,.gif,.bmp", // Ekstensi file yang diterima
               // Tambahkan opsi Dropzone lainnya sesuai kebutuhan
               });

               myDropzone.on("success", function(file, response) {
                   if(response.status) {
                    let html = ""; 
                    
                    //use looping to get all text from response
                    console.log( response.data.text);   
                    response.data.text.forEach((result) => {
                        html += `
                    (${result.page})-----------------------------------------------
                        `;
                         html += result.text;
                    });
                       document.querySelector("#convert-result").value = html;
                   }else{
                          alert(response.message);
                   }
               });

               myDropzone.on("error", function(file, errorMessage) {
               console.error("Error: ", errorMessage);
               });

               console.log(myDropzone);
           });
    </script>
    @endpush
</x-app-layout>