import { Component, ReactNode } from 'react';

interface Props {
  children: ReactNode;
}

interface State {
  hasError: boolean;
}

export default class ErrorBoundary extends Component<Props, State> {
  state: State = { hasError: false };

  static getDerivedStateFromError(): State {
    return { hasError: true };
  }

  componentDidCatch(error: Error, info: React.ErrorInfo) {
    console.error('Selection app error:', error, info.componentStack);
  }

  render() {
    if (this.state.hasError) {
      return (
        <div className="flex items-center justify-center min-h-[200px] text-center px-6">
          <p className="text-bars-text-subtle text-sm">
            Ocurrio un error al cargar la programacion. Por favor, recarga la pagina.
          </p>
        </div>
      );
    }

    return this.props.children;
  }
}
