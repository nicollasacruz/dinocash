<template>
    <div :class="modelValue && 'modal-open'" class="modal">
      <div class="modal-box overflow-visible">
        <div
          id="div-selecionada"
          v-if="title"
          class="text-2xl text-center -mt-1 mb-2"
        >
          {{ title }}
        </div>
        <div
          @click="emit('update:modelValue', false)"
          class="absolute -right-2 -top-2 text-xl cursor-pointer"
        >
          <XCircleIcon class="w-9 h-9" />
        </div>
        <slot name="default" />
      </div>
    </div>
  </template>
  <script setup lang="ts">
  import { XCircleIcon } from "@heroicons/vue/24/solid";
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
  