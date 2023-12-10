<template>
    <select
        @change="updateValue"
        v-bind="styleClasses"
        :value="value"
        class="text-center select"
        :class="[inputStyle, bordered && ' input-bordered']"
    >
        <option
            v-for="({ label, value }, i) in options"
            :key="i"
            :value="value"
        >
            {{ label }}
        </option>
    </select>
    </template>
<script lang="ts" setup>
import { ref, computed } from "vue";
const props = defineProps({
    options: { type: Array },
    bordered: { type: Boolean, default: true },
    inputStyle: { type: String, default: "" },
    value: { type: [String, Number], default: "" },
});
const componentValue = ref();
const styleClasses = computed(() => {
    return {
        "select-bordered": props.bordered,
    };
});
const emit = defineEmits(["update:modelValue", "update:value"]);
function updateValue(e: any) {
    componentValue.value = e.target.value;
    emit("update:value", componentValue.value);
}
</script>
