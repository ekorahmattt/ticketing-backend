const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json()); 

const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ["GET", "POST"]
  }
});

io.on('connection', (socket) => {
  console.log('Client connected:', socket.id);

  socket.on('disconnect', () => {
    console.log('Client disconnected:', socket.id);
  });
});

app.post('/api/webhook/ticket', (req, res) => {
  const { event, data } = req.body;
  console.log(`Received event [${event}] from CI3:`, data);
  
  io.emit('ticketUpdated', { event, data });
  
  res.status(200).json({ success: true, message: 'Broadcast successful' });
});

const PORT = 3001;
server.listen(PORT, () => {
  console.log(`WebSocket Server running on http://localhost:${PORT}`);
});
