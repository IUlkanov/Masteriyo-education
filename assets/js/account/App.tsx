import { ChakraProvider, extendTheme } from '@chakra-ui/react';
import React from 'react';
import { QueryClient, QueryClientProvider } from 'react-query';
import { ReactQueryDevtools } from 'react-query/devtools';
import RTLProvider from '../back-end/context/RTLProvider';
import ErrorBoundary from '../back-end/errors/ErrorBoundary';
import theme from '../back-end/theme/theme';
import { generatePallete } from '../back-end/utils/colors';
import Router from './router/Router';
import localized from './utils/global';

const App = () => {
	const queryClient = new QueryClient({
		defaultOptions: {
			queries: {
				refetchOnWindowFocus: false,
				refetchOnReconnect: false,
				useErrorBoundary: true,
			},
		},
	});

	const color = localized.settings.styling;
	const initialTheme = extendTheme(
		{
			colors: {
				primary: generatePallete(color.primary_color),
			},
		},
		theme
	);

	return (
		<ChakraProvider theme={initialTheme}>
			<RTLProvider>
				<ErrorBoundary>
					<QueryClientProvider client={queryClient}>
						<ReactQueryDevtools initialIsOpen={false} />
						<Router />
					</QueryClientProvider>
				</ErrorBoundary>
			</RTLProvider>
		</ChakraProvider>
	);
};

export default App;
