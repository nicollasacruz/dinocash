<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { XMarkIcon, Bars3Icon } from "@heroicons/vue/24/solid";
import Background from "../../../storage/imgs/home-page/home-bg2.jpg";
import logoDino from "../../../storage/imgs/admin/logo dino branco painel.svg";
import logoDinoRoxo from "../../../storage/imgs/user/dino-logo.svg";
import UserDrawer from "@/Components/UserDrawer.vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
const userWallet = page.props.auth.user.wallet * 1;
const wallet = ref(userWallet);
window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
    wallet.value = e.message.wallet;
});
watch(
    () => wallet.value,
);
const drawer = ref(false);
</script>

<template>
    <div id="root">
        <div class="h-[100dvh] font-menu flex">
            <div class="drawer col-auto lg:w-96 z-10 absolute lg:hidden">
                <input
                    v-model="drawer"
                    id="my-drawer"
                    type="checkbox"
                    class="drawer-toggle"
                />

                <div class="drawer-side lg:w-96">
                    <label
                        for="my-drawer"
                        aria-label="close sidebar"
                        class="drawer-overlay"
                    ></label>
                    <ul
                        class="menu py-4 lg:w-96 min-h-full bg-[#212121] text-white relative"
                    >
                        <x-mark-icon
                            class="w-6 h-6 cursor-pointer absolute top-3 right-3 z-10 lg:hidden fill-white"
                            @click="drawer = !drawer"
                        />
                        <UserDrawer :wallet="wallet" class="mt-3" />
                    </ul>
                </div>
            </div>
            <div
                class="drawer-content h-[80dvh] flex flex-col relative flex-1 px-4 py-2 lg:px-10 lg:py-8"
                :style="{
                    'background-image': `url('${Background}')`,
                    'background-size': 'cover',
                    'background-position': 'center',
                }"
            >
                <!-- Page content here -->
                <bars3-icon
                    @click="drawer = !drawer"
                    class="w-6 h-6 absolute right-3 top-3 cursor-pointer lg:hidden block z-10 fill-black"
                />
                <img
                    :src="logoDinoRoxo"
                    class="mx-auto mb-5 w-36 lg:w-64"
                    alt=""
                />
                <div class="flex gap-x-6 force-height">
                    <UserDrawer
                        :wallet="wallet"
                        @close="drawer = false"
                        class="hidden lg:block"
                    />
                    <div
                        class="border-8 border-black bg-white rounded-xl flex-1 overflow-x-auto"
                    >
                        <slot :wallet="wallet"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.force-height {
    height: calc(100vh - 110px);
    @media (min-width: 1024px) {
        height: calc(100vh - 200px);
    }
}
</style>
