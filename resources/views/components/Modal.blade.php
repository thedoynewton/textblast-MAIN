<!-- Modal For Message Templates -->
<div id="messageContentModal" class="fixed z-10 inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="grid place-items-center h-full">
        <!-- Modal Content -->
        <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            <!-- Title will be dynamically loaded here -->
                        </h3>
                        <hr class="my-4 border-gray-300">
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 whitespace-pre-wrap" id="modal-message-content">
                                <!-- Message content will be dynamically loaded here -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="close-modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>