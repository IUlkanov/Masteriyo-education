import { Alert, Box, Heading, Stack } from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React from 'react';

const Achievement = () => {
	return (
		<Box>
			<Stack spacing="8">
				<Heading as="h1" size="xl">
					{__('My Achievements', 'masteriyo')}
				</Heading>
				<Box>
					<Alert
						colorScheme={'primary.200'}
						color={'primary.400'}
						borderRadius={5}>
						{__(
							'You have no achievements yet. Enroll in course to get an achievements',
							'masteriyo'
						)}
					</Alert>
				</Box>
			</Stack>
		</Box>
	);
};

export default Achievement;
