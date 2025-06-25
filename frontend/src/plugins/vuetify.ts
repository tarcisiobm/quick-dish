import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { mdi } from 'vuetify/iconsets/mdi';
import '@mdi/font/css/materialdesignicons.css';

export default createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi',
    sets: {
      mdi
    }
  },
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#FF7C35',
          success: '#41CC81',
          error: '#F94E4E',
          error_dense: '#E53E3E',
          alert: '#F8BB40',
          background: '#F8F9FA',
          alt_background: '#F8F9FA',
          input_background: '#EEEFF0',
          title: '#1A202C',
          subtitle: '#2D3748',
          text: '#212529',
          text_low_opacity: '#212428',
          border: '#C8C8C8',
          window_border: '#C8C8C8',
          social_btn_background: '#EEF0EF'

        }
      },
      dark: {
        colors: {
          primary: '#FF7F50',
          success: '#41CC81',
          error: '#F56565',
          error_dense: '#E53E3E',
          alert: '#FFA356',
          background: '#121214',
          alt_background: '#151517',
          input_background: '#1B1B1E',
          title: '#FFF',
          subtitle: '#E2E8F0',
          text: '#F1F3F5',
          text_low_opacity: '#D6D7D9',
          border: '#28282A',
          window_border: '#1E1E20',
          social_btn_background: '#1A1A1D'
        }
      }
    }
  },
  defaults: {
    VBtn: {
      style: [{ textTransform: 'none' }],
      variant: 'elevated',
      color: 'primary',
      class: 'text',
    },
    VTextField: {
      color: 'primary',
      bgColor: 'input_background'
    },
    VCheckbox: {
      color: 'text',
      class: 'text color-text'
    }
  }
});
