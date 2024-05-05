/**
 * plugins/vuetify.js
 *
 * Framework documentation: https://vuetifyjs.com`
 */

// Styles
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

// Composables
import { createVuetify } from 'vuetify'

// Define the custom light theme
const myCustomLightTheme = {
  dark: false, // Set to false for light mode
  colors: {
    background: '#F2F2F2', // Slightly darker background
    surface: '#f8f8f8', // Slightly darker than the previous surface
    'surface-bright': '#faf2ff', // Bright surface color
    'surface-light': '#ff6f61', // Slightly darker light peach
    'surface-variant': '#ffa96e', // Slightly darker orange
    'on-surface-variant': '#333333', // Dark gray for contrast
    primary: '#f9de82', // New primary color based off f9de82
    'primary-darken-1': '#e55d48', // Dark coral
    secondary: '#ffd166', // Orange yellow
    'secondary-darken-1': '#ffab4b', // Dark orange yellow
    error: '#b00020', // Error color
    info: '#2196f3', // Info color
    success: '#4caf50', // Success color
    warning: '#fb8c00', // Warning color
  },

  variables: {
    'border-color': '#000000', // Border color
    'border-opacity': 0.12, // Border opacity
    'high-emphasis-opacity': 0.87, // High emphasis opacity
    'medium-emphasis-opacity': 0.60, // Medium emphasis opacity
    'disabled-opacity': 0.38, // Disabled opacity
    'idle-opacity': 0.04, // Idle opacity
    'hover-opacity': 0.04, // Hover opacity
    'focus-opacity': 0.12, // Focus opacity
    'selected-opacity': 0.08, // Selected opacity
    'activated-opacity': 0.12, // Activated opacity
    'pressed-opacity': 0.12, // Pressed opacity
    'dragged-opacity': 0.08, // Dragged opacity
    'theme-kbd': '#212529', // Theme color for keyboard
    'theme-on-kbd': '#ffffff', // On theme color for keyboard
    'theme-code': '#f5f5f5', // Theme color for code
    'theme-on-code': '#000000', // On theme color for code
  }
}

// Create and export the Vuetify instance
export default createVuetify({
  theme: {
    defaultTheme: 'myCustomLightTheme',
    themes: {
      myCustomLightTheme,
    },
  },
})
