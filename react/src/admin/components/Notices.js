import { store as noticesStore } from '@wordpress/notices';
import { useSelect, useDispatch } from '@wordpress/data';
import { SnackbarList } from '@wordpress/components';

export default function Notices() {
    const notices = useSelect(
        ( select ) =>
            select( noticesStore )
                .getNotices()
                .filter( ( notice ) => notice.type === 'snackbar' ),
        []
    );

    const { removeNotice } = useDispatch( noticesStore );
    
    return (
        <div
            style={{
                position: 'relative',
            }}
        >
            <SnackbarList
                notices={ notices }
                onRemove={ removeNotice }
            />
        </div>
    );
};