<template>
    <div></div>
</template>

<script setup lang="ts">
// @ts-ignore
import DinoGame from "../lib/game/DinoGame.js";
import { onUnmounted } from "vue";
import { defineProps, defineEmits, watch } from "vue";
import { onMounted } from "vue";

document.addEventListener(
  "touchmove",
  function (e) {
    if (e.scale !== 1) {
      e.preventDefault();
    }
  },
  { passive: false }
);

const props = defineProps({
  active: Boolean,
  viciosidade: Boolean,
  isAffiliate: Boolean,
  amount: Number,
});
const emit = defineEmits(["endGame", "finishGame"]);
const windowWidth = window.innerWidth;
const width = windowWidth < 700 ? windowWidth : 700;

const game = new DinoGame(width, 300, props.viciosidade, props.isAffiliate, props.amount);
const isTouchDevice =
  "ontouchstart" in window ||
  navigator.maxTouchPoints > 0 ||
  // @ts-ignore
  navigator.msMaxTouchPoints > 0;

// @ts-ignore
document.addEventListener("endGame", ({ detail }) => {
  emit("endGame", detail);
  // destroy game instance
});
document.addEventListener("finishGame", ({ detail }) => {
  emit("finishGame", detail);
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
    FINISH: { 13: 1 },
  };

  if (keycodes.JUMP[keyCode]) {
    game.onInput("jump");
  } else if (keycodes.DUCK[keyCode]) {
    game.onInput("duck");
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
