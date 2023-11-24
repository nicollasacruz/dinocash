<template>
    <div :class="`form-control w-full`">
        <label
            :class="
                error
                    ? 'text-error'
                    : `label-text text-base-content ${labelStyle} mb-1`
            "
            class="text-sm z-10"
        >
            {{ label }}
        </label>
        <input
            :value="modelValue"
            @input="($event) => emit('update:modelValue', $event)"
            class="input w-full bordered"
            :class="[
                error ? 'input-error ' : `${color} ${bgColor}`,
                bordered && 'input-bordered',
                inputStyle,
            ]"
        />
        <div class="text-error text-xs" v-if="error">
            {{ error }}
        </div>
    </div>
</template>

<script setup lang="ts">
// import { MaskType } from "maska";
import { computed } from "vue";
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
        default: "base-100",
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

const emit = defineEmits(["update:modelValue", "update:selected"]);

function updateSelected(e: any) {
    emit("update:selected", e);
}
function handleEvent(e: any) {
    teste(e.target.value);
}
function teste(e: any) {
    emit("update:modelValue", e);
}
</script>
