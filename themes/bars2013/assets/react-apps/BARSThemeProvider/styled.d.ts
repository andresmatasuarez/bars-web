import 'styled-components';

import { BARSTheme } from './theme';

declare module 'styled-components' {
  // Empty on purpose: this declaration merges BARSTheme into styled-components' theme type.
  // eslint-disable-next-line @typescript-eslint/no-empty-object-type
  export interface DefaultTheme extends BARSTheme {}
}
