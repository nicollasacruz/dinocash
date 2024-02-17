<script setup>
import { ref } from "vue";
import Abertura from "./Welcome/Abertura.vue";
import About from "./Welcome/About.vue";
import ComoJogar from "./Welcome/ComoJogar.vue";
import Ranking from "./Welcome/Ranking.vue";
import Reward from "./Welcome/Reward.vue";

import Footer from "./Welcome/Footer.vue";
import UserHeader from "@/Components/UserHeader.vue";
import UserDrawer from "@/Components/UserDrawer.vue";

const { rankedUsers } =
    defineProps({
        rankedUsers: {
            type: Array,
            required: true,
        },
    });
const windowWidth = ref(window.innerWidth);

window.addEventListener("resize", (valu) => {
    windowWidth.value = window.innerWidth;
});
const drawer = ref(false);

</script>

<template>
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
                    class="menu py-4 lg:w-96 min-h-full bg-[#17101F] text-white relative"
                >
                    <x-mark-icon
                        class="w-6 h-6 cursor-pointer absolute top-3 right-3 z-10 lg:hidden fill-white"
                        @click="drawer = !drawer"
                    />
                    <UserDrawer :wallet="wallet" class="mt-3 !bg-[#17101F]" />
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
                @toggle="drawer = !drawer"
                :logged="!!$page?.props?.auth?.user?.id"
                :wallet="
                    !!$page?.props?.auth?.user?.id
                        ? $page.props.auth.user.wallet
                        : 0
                "
                :name="
                    !!$page?.props?.auth?.user?.id
                        ? $page.props.auth.user.name
                        : ''
                "
            />
            <div class="user-height">
                <UserDrawer
                    :wallet="wallet"
                    @close="drawer = false"
                    v-if="!!$page?.props?.auth?.user?.id"
                    class="hidden"
                />
                <div
                    class="bg-[#16101E] text-roxo-claro rounded-xl flex-1 overflow-x-auto font-lighter"
                >
                    <Abertura />
                    <About />
                    <ComoJogar />
                    <Ranking :ranked-users="rankedUsers" />
                    <Reward />
                    <Footer />
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.boxShadow {
    box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
    -webkit-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
    -moz-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
}

.content {
    max-width: 1920px;
    height: 100%;
}

.section1 {
    background-color: rgb(108, 86, 124);
}

.section2 {
    background-color: rgb(144, 188, 175);
}

.section3 {
    background-color: rgb(240, 225, 168);
}

.section4 {
    background-color: rgb(240, 225, 168);
}
</style>
