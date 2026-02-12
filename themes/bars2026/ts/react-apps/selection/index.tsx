import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import App from './components/App';
import DataProvider from './data/DataProvider';

const root = createRoot(document.getElementById('react-root-selection')!);
root.render(
  <StrictMode>
    <DataProvider>
      <App />
    </DataProvider>
  </StrictMode>,
);
