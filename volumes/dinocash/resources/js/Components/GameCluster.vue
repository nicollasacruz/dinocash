<template>
    <teleport to="#app">
        <div
            v-if="gameReady"
            id="info-text"
            class="start-game-text w-screen text-center font-menu"
        >
            {{ text }}
        </div>
    </teleport>
</template>

<script setup lang="ts">
// @ts-ignore
import DinoGame from "../lib/game/DinoGame.js";
import { defineProps, defineEmits, watch, ref, onUnmounted } from "vue";
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
// @ts-ignore
document.addEventListener("endGame", ({ detail }) => {
    emit("endGame", detail);
    // destroy game instance
});
document.addEventListener("finishGame", ({ detail }) => {
    emit("finishGame", detail);
    // destroy game instance
});

const gameReady = ref(false);

game.start().then(() => {
    gameReady.value = true;
}).catch(console.error);

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

document.addEventListener("touchstart", touchStartCallback);
document.addEventListener("touchend", touchEndCallback);
document.addEventListener("keydown", keydownCallback);
document.addEventListener("keyup", keyupCallback);

onUnmounted(() => {
    // removeKeys();
});
function detectPowerSavingMode() {
    // for iOS/iPadOS Safari, and maybe MacBook macOS Safari (not tested)
    if (/(iP(?:hone|ad|od)|Mac OS X)/.test(navigator.userAgent)) {
        // In Low Power Mode, cumulative delay effect happens on setInterval()
        return new Promise((resolve) => {
            let fps = 60;
            let interval = 1000 / fps;
            let numFrames = 30;
            let startTime = performance.now();
            let i = 0;
            let handle = setInterval(() => {
                if (i < numFrames) {
                    i++;
                    return;
                }
                clearInterval(handle);
                let actualInterval =
                    (performance.now() - startTime) / numFrames;
                let ratio = actualInterval / interval; // 1.3x or more in Low Power Mode, 1.1x otherwise
                // alert(actualInterval+' '+interval);
                console.log(actualInterval, interval, ratio);
                resolve(ratio > 1.3);
            }, interval);
        });
    }
    // for Safari, Chromium, and maybe future Firefox
    return detectFrameRate().then((frameRate) => {
        // In Battery Saver Mode frameRate will be about 30fps or 20fps,
        // otherwise frameRate will be closed to monitor refresh rate (typically 60Hz)
        if (frameRate < 34) {
            return true;
        }
        // FIXME fallback to regard as Low Power Mode when battery power is low (down to 20%)
        else if (navigator.getBattery) {
            return navigator.getBattery().then((battery) => {
                return !battery.charging && battery.level <= 0.2 ? true : false;
            });
        }
        return undefined;
    });
}

function detectFrameRate() {
    return new Promise((resolve) => {
        let numFrames = 30;
        let startTime = performance.now();
        let i = 0;
        let tick = () => {
            if (i < numFrames) {
                i++;
                requestAnimationFrame(tick);
                return;
            }
            let frameRate =
                numFrames / ((performance.now() - startTime) / 1000);
            resolve(frameRate);
        };
        requestAnimationFrame(() => {
            tick();
        });
    });
}

// setInterval(async () => {
//     const isPowerSavingMode = detectPowerSavingMode();
//     if (isPowerSavingMode) {
//         emit("finishGame", 0);
//     }
// }, 2000);
</script>
