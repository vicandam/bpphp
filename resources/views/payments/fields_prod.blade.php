<div id="card-panel" class="flex flex-col rounded-bl-md rounded-br-md bg-white p-8 pt-5 shadow-md font-medium">
    {{-- Cardholder name fields --}}
    <div class="mb-2">
        <input
            type="text"
            id="card-holder-first-name"
            name="card_holder_first_name"
            class="w-full rounded-md border border-gray-300 p-3 text-sm mb-2"
            placeholder="Cardholder First Name"
            required
        />
        <input
            type="text"
            id="card-holder-last-name"
            name="card_holder_last_name"
            class="w-full rounded-md border border-gray-300 p-3 text-sm mb-2"
            placeholder="Cardholder Last Name"
            required
        />
        <input
            type="email"
            id="card-holder-email"
            name="card_holder_last_name"
            class="w-full rounded-md border border-gray-300 p-3 text-sm"
            placeholder="Cardholder Email"
            required
        />
    </div>

    <div
        id="payment-form"
        class="mb-4 flex flex-col overflow-hidden rounded-md border border-gray-300 bg-gray-100 shadow-sm"
    >
        <div class="flex border-b border-gray-300">
            <div class="flex w-full flex-col">
                <div class="flex flex-col">
                    <div class="relative flex">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            data-slot="icon"
                            class="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                        >
                            <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                            <path
                                fill-rule="evenodd"
                                d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <input
                            type="text"
                            id="card-number"
                            name="card-number"
                            class="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                            placeholder="Card number"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="flex">
                <div class="flex w-1/2">
                    <input
                        type="text"
                        id="card-exp-month"
                        name="card-exp-month"
                        class="w-14 border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                        placeholder="MM"
                        maxLength="2"
                    />
                    <span class="self-center px-3 font-bold text-gray-500">
                                        /
                                    </span>
                    <input
                        type="text"
                        id="card-exp-year"
                        name="card-exp-year"
                        class="w-auto border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                        placeholder="YYYY"
                        maxLength="4"
                    />
                </div>
                <div class="relative flex w-1/2 border-l border-gray-300">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        data-slot="icon"
                        class="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-green-500"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <input
                        type="text"
                        id="card-cvn"
                        name="card-cvn"
                        class="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                        placeholder="CVV"
                        maxLength="4"
                    />
                </div>
            </div>
        </div>
    </div>
    <div
        id="errorDiv"
        class="hidden col-span-6 mb-4 justify-center gap-x-4 rounded-md bg-red-200 p-3 font-medium text-red-800"
    >
        <span id="error-message">Card error</span>
    </div>
    <div class="col-span-6 flex items-center gap-x-4 rounded-md border border-gray-300 p-4 text-sm font-medium">
        <label
            for="save-card-checkbox"
            class="order-2"
        >
            Save my information for faster checkout
        </label>
        <input
            id="save-card-checkbox"
            type="checkbox"
        />
    </div>
    <div class="mt-4 flex flex-col gap-4">
        <button
            type="button"
            id="charge-card-btn"
            class="w-full rounded-md text-sm bg-black py-3 font-bold uppercase text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black"
        >
            Charge Card
        </button>
        <p class="text-center text-xs text-gray-500 italic">
            * 100% Secure &amp; Safe Payments *
        </p>
    </div>
</div>
