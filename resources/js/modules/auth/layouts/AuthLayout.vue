<template>
    <div class="flex flex-row  items-center">
        <!-- Left: Image -->
        <div class="w-1/2 hidden lg:block ">
            <img
                src="https://placehold.co/800x/667fff/ffffff.png?text=Your+Image&font=Montserrat"
                alt="Placeholder Image"
                class="object-cover object-center rounded-3xl max-h-[520px]"
            />
        </div>
        <!-- Right: Login Form -->
        <div class="flex w-full lg:w-1/2 justify-center items-center">
            <RouterView />
        </div>
    </div>
</template>
