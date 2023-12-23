import Bird from "../actors/Bird.js";
import Cactus from "../actors/Cactus.js";
import Cloud from "../actors/Cloud.js";
import Dino from "../actors/Dino.js";
import sprites from "../sprites.js";
import { playSound } from "../sounds.js";
import {
    loadFont,
    loadImage,
    getImageData,
    randBoolean,
    randInteger,
} from "../utils.js";
import GameRunner from "./GameRunner.js";
import axios from "axios";
import bgJogo from "../../../../storage/imgs/user/bg-jogo.jpg";
import bgNatal from "../../../../storage/imgs/user/bg-natal.jpg";
import bgNatalMobile from "../../../../storage/imgs/user/bg-natal-mobile.jpg";
import gorro from "../../../../storage/imgs/user/gorro.png";
import logo from "../../../../storage/imgs/user/logo.svg";
export default class DinoGame extends GameRunner {
    constructor(width, height, viciosity, isAffiliate, userId) {
        super();
        this.steps = 0;
        this.amount = 0;
        this.userId = userId;
        this.isAffiliate = isAffiliate;
        this.width = null;
        this.height = null;
        this.canvas = this.createCanvas(width, height);
        this.canvasCtx = this.canvas.getContext("2d");
        this.spriteImage = null;
        this.spriteImageData = null;
        this.defaultSettings = {
            bgSpeed: this.isAffiliate ? 7 : this.viciosity ? 9 : 8, // ppf
            birdSpeed: 12, // ppf
            birdSpawnRate: 340, // fpa
            birdWingsRate: 15, // fpa
            cactiSpawnRate: this.isAffiliate
                ? 45
                : this.viciosity
                    ? randInteger(15, 25)
                    : randInteger(25, 40), // fpa
            cloudSpawnRate: 200, // fpa
            cloudSpeed: 2, // ppf
            dinoGravity: this.isAffiliate
                ? 0.7
                : this.viciosity
                    ? 0.78
                    : randInteger(70, 80) / 100, // ppf
            dinoGroundOffset: 4, // px
            dinoLegsRate: 6, // fpa - 6
            dinoLift: this.isAffiliate ? 10 : this.viciosity ? 9 : 9.6, // ppf
            scoreBlinkRate: 20, // fpa
            scoreIncreaseRate: this.isAffiliate
                ? 7
                : this.viciosity
                    ? 10
                    : randInteger(7, 9), // fpa
        };
        this.state = {
            settings: { ...this.defaultSettings },
            birds: [],
            cacti: [],
            clouds: [],
            dino: null,
            gameOver: false,
            groundX: 0,
            groundY: 0,
            isRunning: false,
            level: 0,
            score: {
                blinkFrames: 0,
                blinks: 0,
                isBlinking: false,
                value: 0,
            },
            gorro: null,
        };
    }

    // ref for canvas pixel density:
    // https://developer.mozilla.org/en-US/docs/Web/API/Window/devicePixelRatio#correcting_resolution_in_a_%3Ccanvas%3E
    createCanvas(width, height) {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        const scale = window.devicePixelRatio;

        this.width = width;
        this.height = height;
        canvas.style.width = width + "px";
        canvas.style.height = height + "px";
        canvas.width = Math.floor(width * scale);
        canvas.height = Math.floor(height * scale);
        canvas.style.border = "8px solid #303B69";
        canvas.style.borderRadius = "8px";
        canvas.style.setProperty("-webkit-touch-callout", "none");
        canvas.style.setProperty("-webkit-user-select", "none");
        canvas.style.setProperty("-khtml-user-select", "none");
        canvas.style.setProperty("-moz-user-select", "none");
        canvas.style.setProperty("-ms-user-select", "none");
        canvas.style.setProperty("user-select", "none");
        // shadow bottom with margin left
        canvas.style.boxShadow = "7px 9px 0px 0px rgba(0, 0, 0, 0.85)";
        ctx.scale(scale, scale);
        const app = document.getElementById("app");
        // create div to center canvas
        const canvasContainer = document.createElement("div");
        canvasContainer.id = "canvasContainer";
        canvasContainer.style.display = "flex";
        canvasContainer.style.flexDirection = "column";
        canvasContainer.style.justifyContent = "space-evenly";
        canvasContainer.style.alignItems = "center";
        canvasContainer.style.width = "100%";
        canvasContainer.style.height = "100vh";
        canvasContainer.style.position = "relative";
        canvasContainer.style.overflow = "hidden";
        canvasContainer.style.setProperty("-webkit-touch-callout", "none");
        canvasContainer.style.setProperty("-webkit-user-select", "none");
        canvasContainer.style.setProperty("-khtml-user-select", "none");
        canvasContainer.style.setProperty("-moz-user-select", "none");
        canvasContainer.style.setProperty("-ms-user-select", "none");
        canvasContainer.style.setProperty("user-select", "none");

        var windowWidth = window.innerWidth;

        window.addEventListener("resize", (valu) => {
            windowWidth = window.innerWidth;
        });

        app.style.backgroundImage = `url('${windowWidth < 700 ? bgNatalMobile : bgNatal
            }')`;
        app.style.backgroundSize = windowWidth < 700 ? 'auto 100vh' : 'auto auto';
        app.style.backgroundPosition = 'center';
        app.style.backgroundRepeat = 'no-repeat';
        app.style.backgroundPosition = "center";
        app.style.setProperty("-webkit-touch-callout", "none");
        app.style.setProperty("-webkit-user-select", "none");
        app.style.setProperty("-khtml-user-select", "none");
        app.style.setProperty("-moz-user-select", "none");
        app.style.setProperty("-ms-user-select", "none");
        app.style.setProperty("user-select", "none");
        const image = new Image();
        image.classList.add("w-24", "lg:w-64");
        image.src = logo;
        const div = document.createElement("div");

        div.classList.add(
            "font-menu",
            "text-[#3B2B45]",
            "text-lg",
            "lg:text-xl",
            "text-center",
            "mt-1"
        );
        const felizNatal = document.createElement("div");
        felizNatal.classList.add("text-xl", "lg:text-6xl");
        felizNatal.style.fontSize = "40px";
        felizNatal.style.marginBottom = "-36px";
        felizNatal.style.setProperty("-webkit-touch-callout", "none");
        felizNatal.style.setProperty("-webkit-user-select", "none");
        felizNatal.style.setProperty("-khtml-user-select", "none");
        felizNatal.style.setProperty("-moz-user-select", "none");
        felizNatal.style.setProperty("-ms-user-select", "none");
        felizNatal.style.setProperty("user-select", "none");
        felizNatal.textContent = "Feliz Natal!";
        const prosperoAno = document.createElement("span");
        prosperoAno.textContent = "e um Próspero Ano Novo!";
        div.appendChild(felizNatal);
        // insert line break
        div.appendChild(document.createElement("br"));
        div.appendChild(prosperoAno);
        canvasContainer.appendChild(image);
        canvasContainer.appendChild(canvas);
        canvasContainer.appendChild(div);
        app.prepend(canvasContainer);
        return canvas;
    }
    async preloadImages() {
        const gorroImage = new Image();
        gorroImage.src = gorro;

        await new Promise((resolve) => {
            gorroImage.onload = resolve;
        });

        this.state.gorro = gorroImage;
    }

    setupUI() {
        const container = document.getElementById("buttonContainer");
        if (container) {
            return;
            document.removeChild(container);
        }
        const buttonContainer = document.createElement("div");
        buttonContainer.id = "buttonContainer";
        buttonContainer.style.position = "absolute";
        buttonContainer.style.setProperty("-webkit-touch-callout", "none");
        buttonContainer.style.setProperty("-webkit-user-select", "none");
        buttonContainer.style.setProperty("-khtml-user-select", "none");
        buttonContainer.style.setProperty("-moz-user-select", "none");
        buttonContainer.style.setProperty("-ms-user-select", "none");
        buttonContainer.style.setProperty("user-select", "none");
        buttonContainer.style.top = "18vh";
        buttonContainer.style.width = "100%";
        buttonContainer.style.display = "flex";
        buttonContainer.style.justifyContent = "center";

        const finishButton = document.createElement("button");
        finishButton.style.padding = "8px";
        finishButton.style.fontSize = "25px";
        finishButton.style.backgroundColor = "#d6f8b8";
        finishButton.style.color = "black";
        finishButton.style.fontWeight = 500;
        finishButton.style.fontFamily = "Upheavtt, sans-serif";
        finishButton.style.minWidth = "350px";
        finishButton.style.maxWidth = "90%";
        finishButton.style.border = "2px solid #000";
        finishButton.style.cursor = "pointer";
        finishButton.style.borderRadius = "8px";
        finishButton.style.setProperty("-webkit-touch-callout", "none");
        finishButton.style.setProperty("-webkit-user-select", "none");
        finishButton.style.setProperty("-khtml-user-select", "none");
        finishButton.style.setProperty("-moz-user-select", "none");
        finishButton.style.setProperty("-ms-user-select", "none");
        finishButton.style.setProperty("user-select", "none");
        // Adicione a sombra à caixa
        finishButton.style.boxShadow = "10px 10px 0px 0px rgba(0, 0, 0, 0.85)";
        finishButton.style.webkitBoxShadow =
            "10px 10px 0px 0px rgba(0, 0, 0, 0.85)";
        finishButton.style.mozBoxShadow =
            "10px 10px 0px 0px rgba(0, 0, 0, 0.85)";

        // Adicione margens à caixa para se parecer com o botão fornecido
        finishButton.style.margin = "auto";
        finishButton.style.marginBottom = "4px"; // Ajuste conforme necessário
        finishButton.addEventListener("click", () => {
            const eventoModificacao = new CustomEvent("finishGame", {
                detail: this.state.score.value,
            });
            document.dispatchEvent(eventoModificacao);
            this.endGame();
        });
        const canvasContainer = document.getElementById("canvasContainer");
        if (canvasContainer) {
            buttonContainer.appendChild(finishButton);
            canvasContainer.appendChild(buttonContainer);
        }
    }

    async preload() {
        this.preloadImages();
        const { settings } = this.state;
        const [spriteImage] = await Promise.all([
            loadImage("/sprite.png"),
            loadFont("/PressStart2P-Regular.ttf", "PressStart2P"),
        ]);
        this.spriteImage = spriteImage;
        this.spriteImageData = getImageData(spriteImage);
        const dino = new Dino(this.spriteImageData);

        dino.legsRate = settings.dinoLegsRate;
        dino.lift = settings.dinoLift;
        dino.gravity = settings.dinoGravity;
        dino.x = 25;
        dino.baseY = this.height - settings.dinoGroundOffset;
        this.state.dino = dino;
        this.state.groundY = this.height - sprites.ground.h / 2;
    }

    onFrame() {
        const { state } = this;

        setTimeout(() => {
            this.drawBackground();
        });
        // this.drawFPS()
        this.drawGround();
        this.drawClouds();
        this.drawDino();
        this.drawScore();
        this.drawSantaHat();
        if (state.isRunning) {
            this.setupUI();

            this.drawCacti();

            // if (state.level > randInteger(2, 5)) {
            //     this.drawBirds();
            // }

            if (state.dino.hits([state.cacti[0], state.birds[0]])) {
                playSound("game-over");
                state.gameOver = true;
            }

            if (state.gameOver) {
                this.endGame();
                const eventoModificacao = new CustomEvent("endGame", {
                    detail: this.state.score.value,
                });
                document.dispatchEvent(eventoModificacao);
            } else {
                this.updateScore();
            }

            if (state.finishGame) {
                this.endGame();
            } else {
                this.updateScore();
            }
        }
    }

    onInput(type) {
        const { state } = this;

        switch (type) {
            case "jump": {
                if (state.isRunning) {
                    if (state.dino.jump()) {
                        playSound("jump");
                    }
                } else {
                    this.resetGame();
                    state.dino.jump();
                    playSound("jump");
                }
                break;
            }

            case "duck": {
                if (state.isRunning) {
                    state.dino.duck(true);
                }
                break;
            }

            case "stop-duck": {
                if (state.isRunning) {
                    state.dino.duck(false);
                }
                break;
            }

            // case "finish": {
            //     if (state.isRunning) {
            //         state.finishGame = true;
            //     }
            //     break;
            // }
        }
    }

    resetGame() {
        const text = document.getElementById("info-text");
        text.style.display = "none";
        this.getAmount();
        if (this.state.dino) {
            this.state.dino.reset();
        } else {
            location.reload();
            this.resetGame();
        }
        Object.assign(this.state, {
            settings: { ...this.defaultSettings },
            birds: [],
            cacti: [],
            gameOver: false,
            isRunning: true,
            level: 0,
            score: {
                blinkFrames: 0,
                blinks: 0,
                isBlinking: false,
                value: 0,
            },
        });
        this.preload();
        this.start();
        // this.setupUI();
        const canvasContainer = document.getElementById("canvasContainer");
        canvasContainer.style.display = "flex";
    }

    endGame() {
        const iconSprite = sprites.replayIcon;
        const padding = 15;

        this.paintText(
            "G A M E  O V E R",
            this.width / 2,
            this.height / 2 - padding,
            {
                font: "PressStart2P",
                size: "12px",
                align: "center",
                baseline: "bottom",
                color: "#535353",
            }
        );

        this.paintSprite(
            "replayIcon",
            this.width / 2 - iconSprite.w / 4,
            this.height / 2 - iconSprite.h / 4 + padding
        );

        this.state.isRunning = false;
        this.drawScore();
        this.stop();
        const canvasContainer = document.getElementById("canvasContainer");
        canvasContainer.style.display = "none";
    }

    increaseDifficulty() {
        const { birds, cacti, clouds, dino, settings } = this.state;
        const { bgSpeed, cactiSpawnRate, dinoLegsRate } = settings;
        const { level } = this.state;

        // console.log('bgSpeed', settings.bgSpeed, 'cactiSpawnRate', settings.cactiSpawnRate, 'dinoLegsRate', dinoLegsRate);
        if (level >= 2 && level <= 4) {
            settings.bgSpeed = this.isAffiliate ? settings.bgSpeed * 1.01 : this.viciosity ? settings.bgSpeed + 1 : settings.bgSpeed * 1.1;
            // settings.birdSpeed = settings.bgSpeed * 0.8;
            settings.cactiSpawnRate = this.viciosity ? Math.floor(settings.cactiSpawnRate * 0.9) : settings.cactiSpawnRate;
        } else if (level >= 5) {
            settings.bgSpeed = this.isAffiliate ? settings.bgSpeed * 1.05 : this.viciosity ? settings.bgSpeed + 1 : settings.bgSpeed * (randInteger(11, 15) / 10);
            // settings.birdSpeed = settings.bgSpeed * 0.9;
            settings.cactiSpawnRate = this.isAffiliate ? Math.floor(settings.cactiSpawnRate * 1.1) : this.viciosity ? Math.floor(settings.cactiSpawnRate * 0.8) : Math.floor(settings.cactiSpawnRate * (randInteger(9, 10) / 10));
            if (
                level >= 8 &&
                level % 2 === 0 &&
                dinoLegsRate >= 3 &&
                !this.isAffiliate
            ) {
                settings.dinoLegsRate--;
            }
        }

        for (const bird of birds) {
            bird.speed = settings.birdSpeed;
        }

        for (const cactus of cacti) {
            cactus.speed = settings.bgSpeed;
        }

        for (const cloud of clouds) {
            cloud.speed = settings.bgSpeed;
        }

        dino.legsRate = settings.dinoLegsRate;
    }

    updateScore() {
        const { state } = this;

        if (this.frameCount % state.settings.scoreIncreaseRate === 0) {
            const oldLevel = state.level;

            state.score.value++;
            state.level = Math.floor(state.score.value / 100);
            const button = document.querySelector("button");
            button.textContent = `Clique aqui para sacar: R$${(
                (parseFloat(this.state.score.value) / 500) *
                this.amount
            ).toFixed(2)}`;
            if (state.level !== oldLevel) {
                playSound("level-up");
                this.increaseDifficulty();
                state.score.isBlinking = true;
            }
        }
    }

    drawFPS() {
        this.paintText("fps: " + Math.round(this.frameRate), 0, 0, {
            font: "PressStart2P",
            size: "12px",
            baseline: "top",
            align: "left",
            color: "#535353",
        });
    }
    animate({ content }) {
        const { steps, width, height, canvasCtx } = content;
        const { dino, cacti } = content.state;
        const updateInstances = () => {
            content.paintSprite(dino.sprite, dino.x, dino.y);
            content.drawGround();
            content.drawClouds();
            content.drawScore();
            this.paintInstances(cacti);
        };
        // calculate the current opacity as a percentage
        //     of opacityStep/opacitySteps

        updateInstances();
        const opacitySteps = 90;
        var opacity = 100 * (steps / 90);
        if (steps >= opacitySteps - 1) {
            opacity = 100;
        }
        // clear the canvas
        canvasCtx.clearRect(0, 0, width, height);

        // draw with the starting color using a lessening opacity
        canvasCtx.globalAlpha = (100 - opacity) / 100;
        canvasCtx.fillStyle = "#f7f7f7";
        canvasCtx.fillRect(0, 0, width, height);
        // canvasCtx.strokeRect(0, 0, width, height);

        // draw with the ending color using a increasing opacity
        canvasCtx.globalAlpha = opacity / 100;
        canvasCtx.fillStyle = "#FFA500";
        canvasCtx.fillRect(0, 0, width, height);
        // canvasCtx.strokeRect(0, 0, width, height);
        // content.drawDino();

        // clean up, reset globalAlpha to it's default of 1.00
        canvasCtx.globalAlpha = 1.0;

        // return if all steps have been played

        updateInstances();
        if (steps + 1 >= opacitySteps) {
            content.steps = 90;
            return;
        }
        content.steps++;
        // otherwise request another frame
        requestAnimationFrame(() =>
            this.animate({
                content,
            })
        );
    }
    drawBackground() {
        const { state, steps } = this;
        if (state.score.value < 500) {
            this.canvasCtx.fillStyle = "#f7f7f7";
            this.canvasCtx.fillRect(0, 0, this.width, this.height);
        } else if (state.score.value === 500) {
            this.animate({
                content: this,
            });
        } else if (steps >= 90) {
            this.canvasCtx.fillStyle = "#ebd234";
            this.canvasCtx.fillRect(0, 0, this.width, this.height);
        }
    }

    drawGround(content = this) {
        const { state } = content;
        const { bgSpeed } = state.settings;
        const groundImgWidth = sprites.ground.w / 2;

        this.paintSprite("ground", state.groundX, state.groundY);
        state.groundX -= bgSpeed;

        // append second image until first is fully translated
        if (state.groundX <= -groundImgWidth + this.width) {
            this.paintSprite(
                "ground",
                state.groundX + groundImgWidth,
                state.groundY
            );

            if (state.groundX <= -groundImgWidth) {
                state.groundX = -bgSpeed;
            }
        }
    }

    drawClouds(content = this) {
        const { clouds, settings } = content.state;

        content.progressInstances(clouds);
        if (content.frameCount % settings.cloudSpawnRate === 0) {
            const newCloud = new Cloud();
            newCloud.speed = settings.bgSpeed;
            newCloud.x = content.width;
            newCloud.y = randInteger(20, 80);
            clouds.push(newCloud);
        }
        content.paintInstances(clouds);
    }

    drawDino(content = this) {
        const { dino } = content.state;

        dino.nextFrame();
        content.paintSprite(dino.sprite, dino.x, dino.y);
    }

    drawCacti() {
        const { state } = this;
        const { cacti, settings } = state;

        this.progressInstances(cacti);
        if (this.frameCount % settings.cactiSpawnRate === 0) {
            // randomly either do or don't add cactus
            if (!state.birds.length && randBoolean()) {
                const newCacti = new Cactus(this.spriteImageData);
                newCacti.speed = settings.bgSpeed;
                newCacti.x = this.width;
                newCacti.y = this.height - newCacti.height - 2;
                cacti.push(newCacti);
            }
        }
        this.paintInstances(cacti);
    }

    drawBirds(content = this) {
        const { birds, settings } = content.state;

        content.progressInstances(birds);
        if (content.frameCount % settings.birdSpawnRate === 0) {
            // randomly either do or don't add bird
            if (randBoolean()) {
                const newBird = new Bird(content.spriteImageData);
                newBird.speed = settings.birdSpeed;
                newBird.wingsRate = settings.birdWingsRate;
                newBird.x = content.width;
                // ensure birds are always at least 5px higher than a ducking dino
                newBird.y =
                    content.height -
                    Bird.maxBirdHeight -
                    Bird.wingSpriteYShift -
                    5 -
                    sprites.dinoDuckLeftLeg.h / 2 -
                    settings.dinoGroundOffset;
                birds.push(newBird);
            }
        }
        content.paintInstances(birds);
    }
    drawSantaHat(content = this) {
        const santaHatImage = content.state.gorro;
        const { dino } = content.state;
        content.canvasCtx.drawImage(
            santaHatImage,
            dino.x + 10,
            dino.y - 18,
            30,
            30
        );
    }
    drawScore(content = this) {
        const { canvasCtx, state } = content;
        const { isRunning, score, settings } = state;
        const fontSize = 14;
        let shouldDraw = true;
        let drawValue = score.value;

        if (isRunning && score.isBlinking) {
            score.blinkFrames++;

            if (score.blinkFrames % settings.scoreBlinkRate === 0) {
                score.blinks++;
            }

            if (score.blinks > 7) {
                score.blinkFrames = 0;
                score.blinks = 0;
                score.isBlinking = false;
            } else {
                if (score.blinks % 2 === 0) {
                    drawValue = Math.floor(drawValue / 100) * 100;
                } else {
                    shouldDraw = false;
                }
            }
        }

        if (shouldDraw) {
            // draw the background behind it in case this is called
            // at a time where the background isn't re-drawn (i.e. in `endGame`)
            if (this.state.score.value >= 500) canvasCtx.fillStyle = "#ebd234";
            else canvasCtx.fillStyle = "#f7f7f7";
            canvasCtx.fillRect(
                this.width - fontSize * 5,
                0,
                fontSize * 5,
                fontSize
            );

            this.paintText((drawValue + "").padStart(5, "0"), this.width, 0, {
                font: "PressStart2P",
                size: `${fontSize}px`,
                align: "right",
                baseline: "top",
                color: "#535353",
            });
        }
    }

    /**
     * For each instance in the provided array, calculate the next
     * frame and remove any that are no longer visible
     * @param {Actor[]} instances
     */
    progressInstances(instances) {
        for (let i = instances.length - 1; i >= 0; i--) {
            const instance = instances[i];

            instance.nextFrame();
            if (instance.rightX <= 0) {
                // remove if off screen
                instances.splice(i, 1);
            }
        }
    }
    async getAmount() {
        try {
            const response = await axios.get("/user/lastGame");
            this.amount = response.data.amount * 1;
        } catch (err) {
        }
    }
    /**
     * @param {Actor[]} instances
     */
    paintInstances(instances) {
        for (const instance of instances) {
            this.paintSprite(instance.sprite, instance.x, instance.y);
        }
    }

    paintSprite(spriteName, dx, dy) {
        const { h, w, x, y } = sprites[spriteName];
        this.canvasCtx.drawImage(
            this.spriteImage,
            x,
            y,
            w,
            h,
            dx,
            dy,
            w / 2,
            h / 2
        );
    }

    paintText(text, x, y, opts) {
        const { font = "serif", size = "12px" } = opts;
        const { canvasCtx } = this;

        canvasCtx.font = `${size} ${font}`;
        if (opts.align) canvasCtx.textAlign = opts.align;
        if (opts.baseline) canvasCtx.textBaseline = opts.baseline;
        if (opts.color) canvasCtx.fillStyle = opts.color;
        canvasCtx.fillText(text, x, y);
    }
}
