<template>
    <div></div>
</template>

<script setup lang="ts">
// @ts-ignore
import DinoGame from "../lib/game/DinoGame.js";
import { onUnmounted } from "vue";
import { defineProps, defineEmits, watch } from "vue";
const props = defineProps({
    active: Boolean,
});
const emit = defineEmits(["endGame"]);
const game = new DinoGame(600, 300);
const isTouchDevice =
    "ontouchstart" in window ||
    navigator.maxTouchPoints > 0 ||
    // @ts-ignore
    navigator.msMaxTouchPoints > 0;

// @ts-ignore
document.addEventListener("endGame", ({ detail }) => {
    emit("endGame", detail);
    console.log(detail);
    // destroy game instance
});
game.start().catch(console.error);

const touchStartCallback = ({ touches }) => {
    if (touches.length === 1) {
        game.onInput("jump");
    } else if (touches.length === 2) {
        game.onInput("duck");
    }
};

const touchEndCallback = () => {
    game.onInput("stop-duck");
};

const keydownCallback = ({ keyCode }) => {
    const keycodes = {
        JUMP: { 38: 1, 32: 1 },
        DUCK: { 40: 1 },
    };

    if (keycodes.JUMP[keyCode]) {
        game.onInput("jump");
    } else if (keycodes.DUCK[keyCode]) {
        game.onInput("duck");
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

watch(
    () => props.active,
    (newValue) => {
        if (newValue) {
            document.addEventListener("touchstart", touchStartCallback);
            document.addEventListener("touchend", touchEndCallback);
            document.addEventListener("keydown", keydownCallback);
            document.addEventListener("keyup", keyupCallback);
        } else {
            document.removeEventListener("keydown", keydownCallback);
            document.removeEventListener("keyup", keyupCallback);
            document.removeEventListener("touchstart", touchStartCallback);
            document.removeEventListener("touchend", touchEndCallback);
        }
    }
);
onUnmounted(() => {
    // removeKeys();
});
</script>
