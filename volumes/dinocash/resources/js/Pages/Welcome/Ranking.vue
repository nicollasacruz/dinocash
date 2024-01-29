<template>
    <section
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? bgMobile : Background
            }')`,
            backgroundSize: 'cover',
            backgroundPosition: windowWidth < 700 ? 'bottom' : 'center',
            backgroundRepeat: 'no-repeat',
        }"
        class="h-screen px-3 py-8 lg:px-14"
    >
        <div class="text-5xl md:text-6xl font-menu mb-1 xl:mb-6 ml-4 text-verde">
            Ranking
            <span class="hidden md:inline"> Global </span>
        </div> 
        <div class="grid grid-cols-3 h-[80%]">
            <div class="hidden xl:block col-span-1"></div>
            <div
                class="col-span-3 xl:col-span-2 h-full flex-2 p-1 px-3 mt-3 mb-auto mr-2 flex flex-col items-center bg-[#2A1236] rounded-3xl border-roxo border-4 shadow-xl text-white"
            >
                <div class="flex w-full h-full">
                    <div class="hidden w-60 xl:flex flex-col">
                        <div
                            class="flex justify-center items-end mx-auto mt-14"
                        >
                            <img class="w-7" :src="thirdPlace" />
                            <img class="w-24" :src="firstPlace" />
                            <img class="w-7" :src="secondPlace" />
                        </div>
                        <img
                            :src="DinoRoxo"
                            alt="dinoLogo"
                            class="h-60 -ml-10 mt-auto"
                        />
                    </div>
                    <div class="flex flex-col w-full">
                        <div
                            class="grid grid-cols-4 md:grid-cols-3 md:gap-x-0 gap-y-1 font-menu md:text-xl mt-5 text-verde uppercase text-xs sm:text-md"
                        >
                            <!-- <div class="">Posição</div> -->
                            <div class="col-span-2 md:col-span-1 text-center">
                                Jogador
                            </div>
                            <div class="text-center">Metragem</div>
                            <div class="mb-3 text-center">Prêmio</div>
                        </div>
                        <div
                            class="grid grid-cols-4 md:grid-cols-3 md:gap-x-0 font-menu md:mt-2 text-xs sm:text-md lg:text-lg"
                            :class="
                                rankedUsers.length > 7
                                    ? 'flex-1'
                                    : 'flex-0 gap-y-3'
                            "
                        >
                            <template v-for="(user, i) in rankedUsers">
                                <div
                                    class="flex items-center col-span-2 md:col-span-1 rounded-s-full pl-2 sm:pl-4 overflow-hidden"
                                    :class="
                                        (user.email ===
                                        $page.props.auth.user?.email
                                            ? 'bg-purple-800'
                                            : '',
                                        i < 3 ? 'text-verde' : '')
                                    "
                                >
                                    <span class="mr-2"
                                        >{{ user.posicao }}º</span
                                    >
                                    {{ user.email }}
                                </div>
                                <div
                                    class="flex items-center justify-center text-center"
                                    :class="
                                        (user.email ===
                                        $page.props.auth.user?.email
                                            ? 'bg-purple-800'
                                            : '',
                                        i < 3 ? 'text-verde' : '')
                                    "
                                >
                                    {{ user.distancia }}M
                                </div>
                                <div
                                    class="flex items-center justify-center rounded-r-full"
                                    :class="
                                        (user.email ===
                                        $page.props.auth.user?.email
                                            ? 'bg-purple-800'
                                            : '',
                                        i < 3 ? 'text-verde' : '')
                                    "
                                >
                                    Premio
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from "vue";
import Background from "../../../../storage/imgs/home-page/bg.jpg";
import bgMobile from "../../../../storage/imgs/home-page/bg-mobile.jpg";
const { canLogin, canRegister, laravelVersion, phpVersion, rankedUsers } =
    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
        rankedUsers: {
            type: Array,
            required: true,
        },
    });
const windowWidth = ref(window.innerWidth);

window.addEventListener("resize", (valu) => {
    windowWidth.value = window.innerWidth;
});
</script>
