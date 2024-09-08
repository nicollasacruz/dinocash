<template>
    <div class="flip-clock">
        <span v-for="(tracker, key) in trackers" :key="key" class="flip-clock__piece">
            <b class="flip-clock__card card">
<!--                <b class="card__top">{{ tracker.currentValue }}</b>-->
                <b class="card__bottom">{{ tracker.currentValue }}</b>
            </b>
            <span class="flip-clock__slot">{{ key }}</span>
        </span>
    </div>
</template>

<script setup>

import { ref, onMounted } from 'vue';

function getTimeRemaining(endtime) {
    const t = Date.parse(endtime) - Date.parse(new Date());
    return {
        Total: t,
        Hours: Math.floor((t / (1000 * 60 * 60)) % 24),
        Minutes: Math.floor((t / 1000 / 60) % 60),
        Seconds: Math.floor((t / 1000) % 60),
    };
}

const countdown = ref(null);
const trackers = ref({
    Hours: { currentValue: '00' },
    Minutes: { currentValue: '00' },
    Seconds: { currentValue: '00' },
});

function updateClock() {
    const t = getTimeRemaining(countdown.value);
    if (t.Total < 0) {
        for (let key in trackers.value) {
            trackers.value[key].currentValue = '00';
        }
        return;
    }

    for (let key in trackers.value) {
        trackers.value[key].currentValue = ('0' + t[key]).slice(-2);
    }
}

onMounted(() => {
    countdown.value = new Date(Date.parse(new Date()) + 10 * 60 * 1000);
    setInterval(updateClock, 1000);
});
</script>

<style scoped>
html, body {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #EEE;
}

.flip-clock {
    text-align: center;
    perspective: 400px;
    margin: 0 auto;
}

.flip-clock__piece {
    display: inline-block;
    margin: 0 5px;
}

.flip-clock__slot {
    font-size: 1rem;
    display: none;
}

.card {
    display: block;
    margin: auto 0;
    position: relative;
    font-size: 1.5rem; /* Ajuste para garantir que o texto fique visível */
    width: 40px; /* Largura da carta */
    height: 30px; /* Altura fixa em 100px */
    line-height: 1;
}

.card__top,
.card__bottom,
.card__back::before,
.card__back::after {
    display: block;
    height: 100%; /* Metade da altura do card */
    color: #ccc;
    background: #222;
    padding: 2% 2%;
    border-radius: 5px 5px 5px 5px;
    transform-style: preserve-3d;
    width: 100%;
    transform: translateZ(0);
}

.card__bottom {
    color: #FFF;
    border-top: solid 1px #000;
    background: #393939;
    border-radius: 0 0 5px 5px;
    pointer-events: none;
    overflow: hidden;
}

.card__back {
    position: absolute;
    top: 0;
    height: 100%;
    left: 0%;
    pointer-events: none;
}

.flip .card__back::before {
    animation: flipTop 0.3s cubic-bezier(.37,.01,.94,.35);
    animation-fill-mode: both;
    transform-origin: center bottom;
}

.flip .card__back .card__bottom {
    transform-origin: center top;
    animation-fill-mode: both;
    animation: flipBottom 0.6s cubic-bezier(.15,.45,.28,1);
}

@keyframes flipTop {
    0% {
        transform: rotateX(0deg);
        z-index: 2;
    }
    0%, 99% {
        opacity: 0.99;
    }
    100% {
        transform: rotateX(-90deg);
        opacity: 0;
    }
}

@keyframes flipBottom {
    0%, 50% {
        z-index: -1;
        transform: rotateX(90deg);
        opacity: 0;
    }
    51% {
        opacity: 0.99;
    }
    100% {
        opacity: 0.99;
        transform: rotateX(0deg);
        z-index: 5;
    }
}
</style>
