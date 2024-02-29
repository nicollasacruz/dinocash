<template>
    <teleport to="#app">
        <div
            id="info-text"
            v-if="active"
            class="start-game-text w-screen text-center font-menu"
        >
            {{ text }}
        </div>
    </teleport>
</template>

<script setup lang="ts">
// @ts-ignore
import { onMounted } from "vue";
import DinoGame from "../lib/game/DinoGame.js";
import { defineProps, defineEmits, watch, ref, onUnmounted } from "vue";

const props = defineProps({
    active: Boolean,
    viciosidade: Boolean,
    isAffiliate: Boolean,
    amount: Number,
    start: Boolean,
});
const emit = defineEmits(["endGame", "finishGame", "lockGame"]);
const windowWidth = window.innerWidth;
const windowHeight = window.innerHeight;
const width = windowWidth < 650 ? windowWidth : 650;
const height = windowHeight <= 400 ? 270 : 300;
const text =
    windowWidth > 700 ? "Aperte espaço para começar" : "Toque para começar";
const game = new DinoGame(
    width,
    height,
    props.viciosidade,
    props.isAffiliate,
    props.amount
);

const touchStartCallback = ({ touches }) => {
    // if (touches.length === 1) {
    game.onInput("jump");
    // } else if (touches.length === 2) {
    // game.onInput("duck");
    // }
};

const touchEndCallback = () => {
    game.onInput("stop-duck");
};

const keydownCallback = ({ keyCode }) => {
    const keycodes = {
        JUMP: { 38: 1, 32: 1 },
        DUCK: { 40: 1 },
        FINISH: { 13: 1 },
    };

    if (keycodes.JUMP[keyCode]) {
        game.onInput("jump");
        // } else if (keycodes.DUCK[keyCode]) {
        //   game.onInput("duck");
    } else if (keycodes.FINISH[keyCode]) {
        game.onInput("finish");
    }
};

const keyupCallback = ({ keyCode }) => {
    const keycodes = {
        DUCK: { 40: 1 },
    };

    if (keycodes.DUCK[keyCode]) {
        game.onInput("stop-duck");
    }
};
function setListeners() {
    // @ts-ignore
    document.addEventListener("endGame", ({ detail }) => {
        emit("endGame", detail);
        // destroy game instance
    });
    document.addEventListener("finishGame", ({ detail }) => {
        emit("finishGame", detail);
        // destroy game instance
    });
    document.addEventListener("lockGame", ({ detail }) => {
        emit("lockGame", detail);
        // destroy game instance
    });
    document.addEventListener("touchstart", touchStartCallback);
    document.addEventListener("touchend", touchEndCallback);
    document.addEventListener("keydown", keydownCallback);
    document.addEventListener("keyup", keyupCallback);
    document.addEventListener(
        "touchmove",
        function (e) {
            if (e.scale !== 1) {
                e.preventDefault();
            }
        },
        { passive: false }
    );
}
onUnmounted(() => {
    // removeKeys();
});

onMounted(() => {
    const canvas = document.getElementById("canvasContainer");
    canvas.style.display = "none";
    setListeners();
    document.addEventListener("loaded", () => {
        const canvas = document.getElementById("canvasContainer");
        canvas.style.display = "flex";
        game.start().catch(console.error);
    });
});
</script>
