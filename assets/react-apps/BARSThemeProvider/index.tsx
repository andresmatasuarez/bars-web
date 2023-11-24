import { ReactNode } from 'react';
import { ThemeProvider } from 'styled-components';

import barsDefaultTheme from './theme';

export default function BARSThemeProvider({ children }: { children: ReactNode }) {
  return <ThemeProvider theme={barsDefaultTheme}>{children}</ThemeProvider>;
}
