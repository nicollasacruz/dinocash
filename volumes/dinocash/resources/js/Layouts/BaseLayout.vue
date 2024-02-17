<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import DinoLogo from "../../../storage/imgs/home-page/dino-logo.svg";
import Background1 from "../../../storage/imgs/user/bg-login.jpg";
import BackgroundMobile from "../../../storage/imgs/home-page/home-bg1-mobile.jpg";
import UserHeader from "@/Components/UserHeader.vue";

function isMobile() {
    if (screen.width <= 760) {
        return true;
    }
    return false;
}

function isIpad() {
    if (screen.width > 760 && screen.width < 1000) {
        return true;
    }
    return false;
}

function isPc() {
    if (!isIpad() && !isMobile()) {
        return true;
    }
    return false;
}
const props = defineProps({
    homepage: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? Background1Mobile : Background1
            }')`,
            backgroundSize: windowWidth < 700 ? 'auto 100vh' : 'cover',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
        }"
        class="w-screen h-screen bg-[#6c567c] pb-3 flex flex-col"
        :class="homepage ? '' : 'overflow-hidden'"
    >
        <UserHeader
            :logged="!!$page.props.auth.user?.id"
            @toggle="drawer = !drawer"
            class="mb-3"
        />

        <img
            :src="DinoLogo"
            alt="dinoLogo"
            v-if="!homepage"
            class="h-[14%] my-3 md:mb-5 md:mt-8 mx-auto"
        />

        <slot class="" />
    </div>
</template>
