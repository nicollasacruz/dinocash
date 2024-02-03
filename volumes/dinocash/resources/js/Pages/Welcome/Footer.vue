<template>
    <div class=" text-center">
        <img
            :src="DinoLogo"
            alt="dinoLogo"
            class="h-[20%] w-36 mx-auto z-10 mt-3 mb-4 px-2 md:mt-8"
        />
        <div class="text-center">
            <div class="text-verde font-menu font-bold">
                <a :href="route('terms')">Leia nossos termos de uso!</a>
            </div>
            <div class="text-white text-md md:text-lg">
                Todos os direitos reservados a DinoCash -
                <br class="sm:hidden" />
                2024 Powered by: CashLab
            </div>
            <!-- <div class="text-white mt-3">contato@dinocash.io</div> -->
        </div>
        <img
            :src="cashlab"
            alt="dinoLogo"
            class="h-[20%] w-24 mx-auto z-10 mt-4 mb-5 px-2"
        />
    </div>
</template>

<script setup>
import DinoLogo from "../../../../storage/imgs/home-page/dino-logo.svg";
import cashlab from "../../../../storage/imgs/home-page/logo-cashlab.svg";

</script>
