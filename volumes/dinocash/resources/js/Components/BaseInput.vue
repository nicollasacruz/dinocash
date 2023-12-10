<template>
    <div :class="`'form-control w-full bg-transparent border-none`">
        <label
            :class="error ? `text-error ${labelStyle}` : ` ${labelStyle} mb-1`"
            class="text-xs sm:text-sm z-10"
        >
            {{ label }}
        </label>
        <div class="w-full" v-if="isSelect">
            <base-select
               
                :value="value"
                :inputStyle="inputStyle"
                :bordered="bordered"
                class="w-full bordered bg-[#151515] min-h-[40px] h-[40px]"
                @input="($event) => emit('update:value', $event)"
                :options="props.options"
            />
        </div>
        <input
            v-else
            :value="value"
            @input="($event) => emit('update:value', $event)"
            class="input w-full input-sm bordered"

            :class="[
                error ? 'input-error ' : `${color} ${bgColor}`,
                bordered && 'input-bordered',
                inputStyle,
                classes,
            ]"
            :placeholder="placeholder"
        />
        <div class="text-error text-xs" v-if="error">
            {{ error }}
        </div>
    </div>
</template>

<script setup lang="ts">
// import { MaskType } from "maska";
import { computed } from "vue";
import BaseSelect from "./BaseSelect.vue";
const props = defineProps({
    label: String,
    labelStyle: String,
    classes: String,
    size: {
        type: String,
        default: "md",
    },
    error: {
        default: undefined,
        type: String,
    },
    placeholder: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "base-content",
    },
    bgColor: {
        type: String,
        default: "bg-[#151515]",
    },
    bordered: {
        type: Boolean,
        default: true,
    },
    min: {
        type: [String, Number],
    },
    max: {
        type: [String, Number],
    },
    value: {
        type: [String, Number],
    },
    isCurrency: { type: Boolean },
    options: { type: Array },
    itemsSelected: { type: Object },
    itemsToSelected: { type: Object },
    // dataMaska: { type: String as () => MaskType },
    // dataMaskaReversed: { type: Boolean },
    modelValue: { type: String },
});
const inputStyle = computed(() => {
    const prefix = isSelect.value ? "select" : "input";
    return `${prefix}-${props.size}`;
});

const isAutoComplete = computed(() => !!props.itemsSelected); // Use !! to convert to a boolean value
const isSelect = computed(() => !!props.options?.length); // Use !! to convert to a boolean value

const emit = defineEmits(["update:value", "update:selected"]);

function updateSelected(e: any) {
    emit("update:selected", e);
}
function handleEvent(e: any) {
    teste(e.target.value);
}
function teste(e: any) {
    emit("update:value", e);
}
</script>
