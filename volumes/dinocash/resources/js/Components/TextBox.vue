<template>
    <div>
        <div class="text-center font-bold uppercase mb-1" :class="labelText">
            {{ label }}
        </div>
        <slot name="value">
            <div
                class="flex rounded-md border-[1px] border-gray-500 bg-[#212121] w-full text-white px-2 py-1 font-bold"
            >
                <slot name="icon" />

                <div class="text-center flex-1" :class="valueText">
                    {{ value }}
                </div>
                <slot name="action" />
            </div>
        </slot>
    </div>
</template>
<script setup lang="ts">
import { defineProps } from "vue";
const { label, value, labelText, valueText } = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        // required: true,
    },
    labelText: {
        type: String,
        default: "text-white",
    },
    valueText: {
        type: String,
        default: "text-white",
    },
});
</script>
