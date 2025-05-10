import { getCurrentInstance } from "vue";

export function useNotify() {
    const instance = getCurrentInstance();
    return instance?.appContext.config.globalProperties.notify;
}