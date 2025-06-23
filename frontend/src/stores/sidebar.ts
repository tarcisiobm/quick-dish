import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useSidebarStore = defineStore('sidebar', () => {
  const savedPinned = localStorage.getItem('sidebar-pinned')
  const initialPinned = savedPinned ? JSON.parse(savedPinned) : false

  const isExpanded = ref(initialPinned)
  const isPinned = ref(initialPinned)

  watch(isPinned, (value) => {
    localStorage.setItem('sidebar-pinned', JSON.stringify(value))
    if (value) {
      isExpanded.value = true
    }
  })

  function expandSidebar() {
    isExpanded.value = true
  }

  function collapseSidebar() {
    if (!isPinned.value) {
      isExpanded.value = false
    }
  }

  function togglePin() {
    isPinned.value = !isPinned.value
  }

  function setSidebarExpanded(expanded: boolean) {
    isExpanded.value = expanded
  }

  function setSidebarPinned(pinned: boolean) {
    isPinned.value = pinned
    if (!pinned) {
      isExpanded.value = false
    }
  }

  return {
    isExpanded,
    isPinned,
    expandSidebar,
    collapseSidebar,
    togglePin,
    setSidebarExpanded,
    setSidebarPinned
  }
})
