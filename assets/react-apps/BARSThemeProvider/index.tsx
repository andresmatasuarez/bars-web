import React, { ReactNode } from 'react';
import { ThemeProvider } from 'styled-components';
import barsDefaultTheme from './theme';

function BARSThemeProvider({ children }: { children: ReactNode }) {
  return <ThemeProvider theme={barsDefaultTheme}>{children}</ThemeProvider>;
}

export default BARSThemeProvider;
