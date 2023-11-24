import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import BARSThemeProvider from '../BARSThemeProvider';
import App from './App';
import DataProvider from './data/DataProvider';

const root = createRoot(document.getElementById('react-root-selection')!);
root.render(
  <StrictMode>
    <DataProvider>
      <BARSThemeProvider>
        <App />
      </BARSThemeProvider>
    </DataProvider>
  </StrictMode>,
);
