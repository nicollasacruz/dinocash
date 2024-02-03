<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { XMarkIcon, Bars3Icon } from "@heroicons/vue/24/solid";
import Background from "../../../storage/imgs/user/bg-login.jpg";
import logoDino from "../../../storage/imgs/admin/logo dino branco painel.svg";
import logoDinoRoxo from "../../../storage/imgs/user/dino-logo.svg";
import UserDrawer from "@/Components/UserDrawer.vue";
import { usePage } from "@inertiajs/vue3";
import UserHeader from "@/Components/UserHeader.vue";

const page = usePage();
const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
const userWallet = page.props.auth.user.wallet * 1;
const wallet = ref(userWallet);
window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
    wallet.value = e.message.wallet;
});
const drawer = ref(false);
</script>

<template>
    <div id="root">
        <div class="h-screen font-menu flex">
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
                class="drawer-content h-screen flex flex-col relative flex-1"
                :style="{
                    'background-image': `url('${Background}')`,
                    'background-size': 'cover',
                    'background-position': 'center',
                }"
            >
                <!-- Page content here -->
                <UserHeader
                    :logged="!!$page?.props?.auth?.user?.id"
                    @toggle="drawer = !drawer"
                    :wallet="wallet + $page.props.auth.user.bonusWallet"
                    :name="$page.props.auth.user.name"
                />
                <div class="grid grid-cols-1 xl:grid-cols-12 flex-1">
                    <div
                        class="col-span-1 xl:col-span-10 xl:col-start-2 flex flex-col"
                    >
                        <div
                            class="flex gap-x-6 p-3 py-2 lg:px-10 lg:py-8 user-height"
                        >
                            <UserDrawer
                                :wallet="wallet"
                                @close="drawer = false"
                                class="hidden lg:block"
                            />
                            <div
                                class="bg-[#16101E] text-roxo-claro rounded-xl flex-1 overflow-x-auto font-lighter"
                            >
                                <slot :wallet="wallet" />
                            </div>
                        </div>
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
.user-height {
    height: calc(100vh - 110px);
    @media (min-width: 1024px) {
        height: calc(100vh - 100px);
    }
}
.font-lighter {
    font-family: "FF-Mark", sans-serif;
    src: url("/resources/css/FF-Mark/MarkPro-Thin.otf") format("opentype");
}
</style>
