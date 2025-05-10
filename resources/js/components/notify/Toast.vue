<template>
  <div v-if="visible" :class="['fixed top-4 right-4 z-50 p-4 rounded shadow text-white', typeClass]">
    {{ message }}
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const visible = ref(false)
const message = ref('')
const typeClass = ref('bg-green-500')

const notify = (messageOrOptions: string | { message: string, type?: 'success' | 'error' }, type?: 'success' | 'error') => {
    let msg = ''
    let t: 'success' | 'error' = 'success'
    if (typeof messageOrOptions === 'string') {
        msg = messageOrOptions
        if (type) t = type
    } else {
        msg = messageOrOptions.message
        if (messageOrOptions.type) t = messageOrOptions.type
    }
    message.value = msg
    typeClass.value = t === 'error' ? 'bg-red-500' : 'bg-green-500'
    visible.value = true
    setTimeout(() => visible.value = false, 3000)
}

defineExpose({ notify })
</script>
