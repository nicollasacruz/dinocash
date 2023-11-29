<template>
    <div :class="modelValue && 'modal-open'" class="modal">
      <div class="modal-box overflow-visible bg-[#212121] text-white">
        <div
          id="div-selecionada"
          v-if="title"
          class="text-2xl text-center -mt-1 mb-2"
        >
          {{ title }}
        </div>
        <div
          @click="emit('update:modelValue', false)"
          class="absolute right-2 top-2 text-xl cursor-pointer"
        >
          <XMarkIcon class="w-5 h-5 fill-gray-500" />
        </div>
        <slot name="default" />
      </div>
    </div>
  </template>
  <script setup lang="ts">
  import { XMarkIcon } from "@heroicons/vue/24/solid";
  defineProps({
    modelValue: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: undefined,
    },
  });
  const emit = defineEmits(["update:modelValue"]);
  </script>
  