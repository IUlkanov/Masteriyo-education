import {
	Box,
	Center,
	Heading,
	Stack,
	Text,
	ThemingProps,
} from '@chakra-ui/react';
import React, { ReactNode } from 'react';

interface Props {
	count: number;
	title: string;
	colorScheme: ThemingProps['colorScheme'];
	icon: ReactNode;
}

const CountBox: React.FC<Props> = (props) => {
	const { title, count, colorScheme, icon } = props;

	return (
		<Box
			borderWidth="1px"
			borderColor="gray.100"
			className="mto-dashboard-courses-card-wrapper">
			<Stack
				direction="row"
				spacing="4"
				p="6"
				className="mto-dashboard-courses-card">
				<Stack
					direction="row"
					spacing="4"
					align="center"
					justify="space-between"
					className="mto-dashboard-courses-content">
					<Center
						bg={`${colorScheme}.100`}
						w="16"
						h="16"
						rounded="xl"
						color={`${colorScheme}.500`}
						className="mto-dashboard-courses-content-left">
						{icon}
					</Center>
					<Stack
						direction="column"
						className="mto-dashboard-courses-content-right">
						<Heading size="sm" color="gray.800">
							{title}
						</Heading>
						<Text color={`${colorScheme}.700`} fontWeight="bold" fontSize="md">
							{count}
						</Text>
					</Stack>
				</Stack>
			</Stack>
		</Box>
	);
};

export default CountBox;
