import { useState } from 'react';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Plus, MessageCircle } from 'lucide-react';

interface NewSessionModalProps {
    trigger?: React.ReactNode;
    children?: React.ReactNode;
}

export function NewSessionModal({ trigger, children }: NewSessionModalProps) {
    const [open, setOpen] = useState(false);
    const [sessionName, setSessionName] = useState('');
    const [isCreating, setIsCreating] = useState(false);

    const handleCreateSession = () => {
        if (!sessionName.trim()) {
            return;
        }

        setIsCreating(true);

        // Clear all voiceflow session data from localStorage
        console.log('🧹 CLEARING: Starting new session - removing all voiceflow localStorage data');
        
        const voiceflowKeys = Object.keys(localStorage).filter(key => 
            key.startsWith('voiceflow-session-')
        );
        
        voiceflowKeys.forEach(key => {
            console.log(`🗑️ Removing: ${key}`);
            localStorage.removeItem(key);
        });

        // Navigate to sessions with name and status query parameters
        const params = new URLSearchParams({
            name: sessionName.trim(),
            status: 'new'
        });

        window.location.href = `/sessions?${params.toString()}`;
    };

    const handleKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleCreateSession();
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                {trigger || children || (
                    <Button className="bg-blue-600 hover:bg-blue-700 text-white">
                        <Plus className="h-4 w-4 mr-1" />
                        New Session
                    </Button>
                )}
            </DialogTrigger>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <MessageCircle className="h-5 w-5 text-blue-600" />
                        Start New Session
                    </DialogTitle>
                    <DialogDescription>
                        Give your coaching session a name to help you identify it later.
                    </DialogDescription>
                </DialogHeader>
                <div className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="session-name">Session Name</Label>
                        <Input
                            id="session-name"
                            placeholder="e.g., Leadership Development, Career Planning..."
                            value={sessionName}
                            onChange={(e) => setSessionName(e.target.value)}
                            onKeyPress={handleKeyPress}
                            autoFocus
                            disabled={isCreating}
                        />
                    </div>
                </div>
                <DialogFooter className="sm:justify-between">
                    <Button 
                        variant="outline" 
                        onClick={() => setOpen(false)}
                        disabled={isCreating}
                    >
                        Cancel
                    </Button>
                    <Button 
                        onClick={handleCreateSession}
                        disabled={!sessionName.trim() || isCreating}
                        className="bg-blue-600 hover:bg-blue-700"
                    >
                        {isCreating ? 'Creating...' : 'Start Session'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}